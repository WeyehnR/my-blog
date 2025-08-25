// EasyMDE Rich Text Editor Initialization
(function () {
  // Prevent multiple initialization
  if (window.easyMDEInitialized) {
    return;
  }
  window.easyMDEInitialized = true;

  // Initialize EasyMDE when DOM is ready
  document.addEventListener("DOMContentLoaded", function () {
    const contentTextarea = document.getElementById("content");

    if (!contentTextarea) {
      return; // Exit if textarea not found
    }

    // Basic EasyMDE initialization with image upload support
    const easyMDE = new EasyMDE({
      element: contentTextarea,
      placeholder: "Write your post content here...",
      spellChecker: false,
      initialValue: contentTextarea.value || "",
      uploadImage: true,
      imageMaxSize: 1024 * 1024 * 5, // 5MB max
      imageAccept: "image/png, image/jpeg, image/gif, image/jpg, image/webp",
      imageUploadFunction: function (file, onSuccess, onError) {
        // Custom upload function
        const formData = new FormData();
        formData.append("image", file);

        fetch("/my-blog/public/?url=upload_image", {
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              onSuccess(data.url);
            } else {
              onError(data.error || "Upload failed");
            }
          })
          .catch((error) => {
            console.error("Upload error:", error);
            onError("Network error occurred");
          });
      },
      errorMessages: {
        noFileGiven: "No file selected",
        typeNotAllowed: "This image type is not allowed",
        fileTooLarge: "Image is too large (max 5MB)",
        importError: "Something went wrong when uploading the image",
      },
      toolbar: [
        "bold",
        "italic",
        "strikethrough",
        "|",
        "heading-1",
        "heading-2",
        "heading-3",
        "|",
        "unordered-list",
        "ordered-list",
        "|",
        "link",
        "upload-image", // Changed from "image" to "upload-image" for drag & drop
        "|",
        "code",
        "quote",
        "|",
        "preview",
        "side-by-side",
        "fullscreen",
        "|",
        "guide",
      ],
    });

    // Fix toolbar styling and tooltips
    setTimeout(function () {
      // Remove title attributes that cause white tooltips
      const toolbarButtons = document.querySelectorAll(
        ".editor-toolbar a[title]"
      );
      toolbarButtons.forEach((button) => {
        button.removeAttribute("title");
      });

      // Direct DOM manipulation for transparent hover
      const allToolbarButtons = document.querySelectorAll(
        ".editor-toolbar a, .editor-toolbar button"
      );
      allToolbarButtons.forEach((button) => {
        button.addEventListener("mouseenter", function (e) {
          e.target.style.backgroundColor = "transparent";
          e.target.style.background = "transparent";
        });

        button.addEventListener("mouseleave", function (e) {
          e.target.style.backgroundColor = "transparent";
          e.target.style.background = "transparent";
        });
      });
    }, 500);

    // Auto-save functionality (optional)
    setInterval(function () {
      if (easyMDE) {
        const content = easyMDE.value();
        if (content.trim()) {
          localStorage.setItem(
            "easymde_draft_" + window.location.pathname,
            content
          );
        }
      }
    }, 10000); // Save every 10 seconds

    // Restore draft on page load if no server content exists
    const currentContent = easyMDE.value();
    if (!currentContent.trim()) {
      const draft = localStorage.getItem(
        "easymde_draft_" + window.location.pathname
      );
      if (draft) {
        easyMDE.value(draft);
      }
    }

    // Clear draft when form is successfully submitted
    const form = contentTextarea.closest("form");
    if (form) {
      form.addEventListener("submit", function () {
        localStorage.removeItem("easymde_draft_" + window.location.pathname);
      });
    }

    // Make easyMDE available globally for debugging
    window.easyMDE = easyMDE;
  });
})();
