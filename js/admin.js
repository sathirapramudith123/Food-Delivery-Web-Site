document.addEventListener('DOMContentLoaded', () => {
  // Check if the chart canvas exists on the page
  const canvas = document.getElementById('userBarChart');
  if (!canvas) return;

  // userRoleCounts must be defined in the HTML before this script runs
  const ctx = canvas.getContext('2d');

  const chart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: Object.keys(window.userRoleCounts),
      datasets: [{
        label: 'User Count',
        data: Object.values(window.userRoleCounts),
        backgroundColor: ['#3498db', '#2ecc71', '#e67e22'],
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            precision: 0
          }
        }
      }
    }
  });
});
