// AJAX voting functionality
function vote(postId, type) {
  const xhr = new XMLHttpRequest();
  xhr.open("POST", "/my-blog/public/?url=vote_ajax", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        try {
          const response = JSON.parse(xhr.responseText);
          if (response.success) {
            // Update the vote score
            const scoreElement = document.querySelector(
              `#post-${postId} .vote-score`
            );
            if (scoreElement) {
              scoreElement.textContent = response.score;
            }

            // Update arrow colors
            const upArrow = document.querySelector(`#post-${postId} .vote-up`);
            const downArrow = document.querySelector(
              `#post-${postId} .vote-down`
            );

            // Reset colors
            if (upArrow) upArrow.style.color = "";
            if (downArrow) downArrow.style.color = "";

            // Highlight the voted arrow
            if (response.userVote === "up" && upArrow) {
              upArrow.style.color = "#0079d3";
            } else if (response.userVote === "down" && downArrow) {
              downArrow.style.color = "#0079d3";
            }
          } else if (response.redirect) {
            // User not logged in, redirect to login
            window.location.href = response.redirect;
          }
        } catch (e) {
          console.error("Error parsing response:", e);
        }
      }
    }
  };

  xhr.send(`post_id=${postId}&type=${type}`);
}

// Add event listeners when page loads
document.addEventListener("DOMContentLoaded", function () {
  // Add click handlers to all vote buttons
  document.querySelectorAll(".vote-up, .vote-down").forEach((button) => {
    button.addEventListener("click", function (e) {
      e.preventDefault();
      const postId = this.getAttribute("data-post-id");
      const type = this.classList.contains("vote-up") ? "up" : "down";
      vote(postId, type);
    });
  });
});
