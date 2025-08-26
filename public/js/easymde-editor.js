// EasyMDE Rich Text Editor Initialization
(function () {
  // Prevent multiple initialization
  if (window.easyMDEInitialized) {
    return;
  }
  window.easyMDEInitialized = true;

  // Simple initialization function
  function initializeEasyMDE() {
    // Check if EasyMDE is available
    if (typeof EasyMDE === "undefined") {
      console.error("EasyMDE library not loaded!");
      return;
    }

    const contentTextarea = document.getElementById("content");
    if (!contentTextarea) {
      return; // Exit if textarea not found
    }

    try {
      // Create EasyMDE instance with image upload support
      const easyMDE = new EasyMDE({
        element: contentTextarea,
        placeholder: "Write your post content here...",
        spellChecker: false,

        // Image upload configuration
        uploadImage: true,
        imageMaxSize: 1024 * 1024 * 5, // 5MB max
        imageAccept: "image/png, image/jpeg, image/gif, image/jpg, image/webp",
        imageUploadEndpoint: "/my-blog/public/?url=upload_image",

        // Ensure absolute path handling
        imagePathAbsolute: true,

        // Enable image preview in editor
        previewImagesInEditor: true,

        // Image preview handler to ensure our URLs work in preview panel
        imagesPreviewHandler: function (src) {
          console.log("Image preview handler called with src:", src);

          // For our image URLs, return them as-is - they're already complete
          if (src && src.includes("/my-blog/public/?url=image/")) {
            console.log("Using blog image URL:", src);
            return src;
          }

          // For other URLs, return as-is
          console.log("Using original src:", src);
          return src;
        },

        // Custom preview renderer to ensure images display properly in preview panel
        previewRender: function (plainText, preview) {
          console.log("Preview render called with plainText:", plainText);

          // Use marked library if available, otherwise simple processing
          let html = plainText;

          // Convert markdown to HTML manually for our image URLs
          if (typeof marked !== "undefined") {
            html = marked(plainText);
            console.log("Marked HTML:", html);
          } else {
            // Simple markdown processing for images
            // Replace ![alt](url) with <img> tags - handle our URL format with query params
            html = plainText.replace(
              /!\[([^\]]*)\]\(([^\)]+)\)/g,
              function (match, alt, url) {
                console.log(
                  "Found image markdown:",
                  match,
                  "alt:",
                  alt,
                  "url:",
                  url
                );
                return (
                  '<img src="' +
                  url +
                  '" alt="' +
                  alt +
                  '" style="max-width: 100%; height: auto; display: block; margin: 10px 0;">'
                );
              }
            );

            // Also handle links to our image URLs (without the !) and convert them to images
            html = html.replace(
              /\[([^\]]*)\]\((\/my-blog\/public\/\?url=image\/[^\)]+)\)/g,
              function (match, alt, url) {
                console.log(
                  "Found image link (converting to image):",
                  match,
                  "alt:",
                  alt,
                  "url:",
                  url
                );
                return (
                  '<img src="' +
                  url +
                  '" alt="' +
                  alt +
                  '" style="max-width: 100%; height: auto; display: block; margin: 10px 0;">'
                );
              }
            );

            // Replace **text** with <strong>
            html = html.replace(/\*\*([^\*]+)\*\*/g, "<strong>$1</strong>");

            // Replace *text* with <em>
            html = html.replace(/\*([^\*]+)\*/g, "<em>$1</em>");

            // Replace line breaks with paragraphs for better formatting
            html = html
              .split("\n")
              .map((line) => (line.trim() ? "<p>" + line + "</p>" : ""))
              .join("");
          }

          // Post-process to fix our custom image URLs that might not be recognized
          html = html.replace(
            /<a[^>]*href="([^"]*\/my-blog\/public\/\?url=image\/[^"]*)"[^>]*>([^<]*)<\/a>/g,
            '<img src="$1" alt="$2" style="max-width: 100%; height: auto; display: block; margin: 10px 0;">'
          );

          console.log("Final HTML:", html);
          return html;
        },

        // Toolbar configuration - use correct button references
        toolbar: [
          "bold",
          "italic",
          "heading",
          "|",
          "quote",
          "unordered-list",
          "ordered-list",
          "|",
          "link",
          "image",
          "upload-image",
          "|",
          "preview",
          "side-by-side",
          "fullscreen",
          "|",
          "guide",
        ],

        // Error messages for image upload
        errorMessages: {
          noFileGiven: "No file selected",
          typeNotAllowed: "This image type is not allowed",
          fileTooLarge: "Image is too large (max 5MB)",
          importError: "Something went wrong when uploading the image",
        },

        // Status bar - simple configuration
        status: false,

        // Other options
        minHeight: "300px",
      });

      console.log("EasyMDE initialized successfully");

      // Handle form submission to ensure content is synced
      const form = contentTextarea.closest("form");
      if (form) {
        form.addEventListener("submit", function (e) {
          // Ensure EasyMDE content is synced to the textarea
          const content = easyMDE.value();
          contentTextarea.value = content;

          // Basic validation
          if (!content || content.trim() === "") {
            e.preventDefault();
            alert("Content is required");
            return false;
          }
        });
      }
    } catch (error) {
      console.error("Failed to initialize EasyMDE:", error);
    }
  }

  // Initialize when DOM is ready
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initializeEasyMDE);
  } else {
    initializeEasyMDE();
  }
})();
