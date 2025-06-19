document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('feedbackForm');
  const feedbackList = document.getElementById('feedbackList');
  let selectedRating = 0;

  // Star rating selection
  document.querySelectorAll('.fa-star').forEach(star => {
    star.addEventListener('click', () => {
      selectedRating = parseInt(star.dataset.rating);
      updateStars(selectedRating);
    });
  });

  function updateStars(rating) {
    document.querySelectorAll('.fa-star').forEach(star => {
      star.classList.toggle('checked', parseInt(star.dataset.rating) <= rating);
    });
  }

  // Load existing feedback on page load
  loadFeedback();

  function loadFeedback() {
    fetch('feedback_handler.php?action=read')
      .then(res => res.json())
      .then(data => {
        feedbackList.innerHTML = '';
        data.forEach(item => {
          const div = document.createElement('div');
          div.className = 'list-group-item';
          div.innerHTML = `
            <p>${item.comment}</p>
            <p>Rating: ${'⭐'.repeat(item.rating)}</p>
            <small>${new Date(item.created_at).toLocaleString()}</small>
          `;
          feedbackList.appendChild(div);
        });
      });
  }

  form.addEventListener('submit', e => {
    e.preventDefault();
    const comment = document.getElementById('comment').value.trim();
    if (!comment || selectedRating === 0) {
      alert('Please enter feedback and select a rating.');
      return;
    }

    const formData = new FormData();
    formData.append('comment', comment);
    formData.append('rating', selectedRating);

    fetch('feedback_handler.php?action=create', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(response => {
      if (response.success) {
        form.reset();
        selectedRating = 0;
        updateStars(0);
        loadFeedback();
      } else {
        alert('Error submitting feedback.');
      }
    });
  });
});
