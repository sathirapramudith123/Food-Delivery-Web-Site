document.addEventListener('DOMContentLoaded', () => {
  let selectedRating = 0;
  let editMode = false;
  let editTarget = null;

  const stars = document.querySelectorAll('.fa-star');
  const commentInput = document.getElementById('comment');
  const feedbackForm = document.getElementById('feedbackForm');
  const feedbackList = document.getElementById('feedbackList');

  // Handle star rating selection
  stars.forEach(star => {
    star.addEventListener('click', function () {
      selectedRating = parseInt(this.getAttribute('data-rating'));
      updateStars(selectedRating);
    });
  });

  function updateStars(rating) {
    stars.forEach((s, index) => {
      s.classList.toggle('checked', index < rating);
    });
  }

  // Handle form submission
  feedbackForm.addEventListener('submit', function (e) {
    e.preventDefault();
    const comment = commentInput.value.trim();
    if (!comment || selectedRating === 0) {
      alert('Please enter feedback and select a star rating.');
      return;
    }

    if (editMode && editTarget) {
      // Update mode
      editTarget.querySelector('.feedback-comment').textContent = comment;
      editTarget.querySelector('.feedback-rating').innerHTML =
        '★'.repeat(selectedRating) + '☆'.repeat(5 - selectedRating);
      resetForm();
    } else {
      // Create new feedback item
      const item = document.createElement('div');
      item.className = 'list-group-item d-flex justify-content-between align-items-start flex-column flex-md-row gap-2';

      item.innerHTML = `
        <div>
          <p class="mb-1 feedback-comment">${comment}</p>
          <p class="text-warning mb-1 feedback-rating">${'★'.repeat(selectedRating)}${'☆'.repeat(5 - selectedRating)}</p>
        </div>
        <div>
          <button class="btn btn-sm btn-outline-primary me-2 edit-btn">Edit</button>
          <button class="btn btn-sm btn-outline-danger delete-btn">Delete</button>
        </div>
      `;

      // Add event listeners to edit and delete buttons
      item.querySelector('.edit-btn').addEventListener('click', () => {
        commentInput.value = item.querySelector('.feedback-comment').textContent;
        selectedRating = item.querySelector('.feedback-rating').textContent.split('★').length - 1;
        updateStars(selectedRating);
        editMode = true;
        editTarget = item;
      });

      item.querySelector('.delete-btn').addEventListener('click', () => {
        if (confirm('Are you sure you want to delete this feedback?')) {
          item.remove();
          if (item === editTarget) resetForm();
        }
      });

      feedbackList.prepend(item);
      resetForm();
    }
  });

  function resetForm() {
    commentInput.value = '';
    selectedRating = 0;
    updateStars(0);
    editMode = false;
    editTarget = null;
  }
});
