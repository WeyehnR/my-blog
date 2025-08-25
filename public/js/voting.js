// AJAX voting functionality - Using event delegation
(function () {
  // Prevent multiple initialization
  if (window.votingInitialized) {
    return;
  }
  window.votingInitialized = true;

  // Use event delegation on the document body to capture all vote buttons
  document.body.addEventListener("click", function (e) {
    // Check if clicked element is a vote button
    const button = e.target;

    if (
      button.classList.contains("vote-btn") &&
      !button.classList.contains("disabled")
    ) {
      e.preventDefault();
      e.stopPropagation();

      // Prevent multiple rapid clicks
      if (button.classList.contains("processing")) {
        return;
      }

      const postId = button.getAttribute("data-post-id");
      const voteType = button.getAttribute("data-vote-type");

      if (postId && voteType) {
        // Mark as processing
        button.classList.add("processing");
        button.style.opacity = "0.6";

        vote(postId, voteType, button);
      }
    }
  });
})();

function vote(postId, type, clickedButton) {
  const xhr = new XMLHttpRequest();
  xhr.open("POST", "/my-blog/public/?url=vote_ajax", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      // Re-enable the button
      if (clickedButton) {
        clickedButton.classList.remove("processing");
        clickedButton.style.opacity = "";
      }

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
            const upArrow = document.querySelector(
              `#post-${postId} .vote-btn.upvote`
            );
            const downArrow = document.querySelector(
              `#post-${postId} .vote-btn.downvote`
            );

            // Reset colors
            if (upArrow) upArrow.style.color = "";
            if (downArrow) downArrow.style.color = "";

            // Highlight the voted arrow
            if (response.userVote === "up" && upArrow) {
              upArrow.style.color = "#ff4500";
            } else if (response.userVote === "down" && downArrow) {
              downArrow.style.color = "#7193ff";
            }
          } else if (response.redirect) {
            // User not logged in, redirect to login
            window.location.href = response.redirect;
          }
        } catch (e) {
          // Error parsing response
        }
      }
    }
  };

  xhr.send(`post_id=${postId}&vote_type=${type}`);
}
