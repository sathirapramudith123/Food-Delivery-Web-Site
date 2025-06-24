    // Simulate data fetching
    document.getElementById('userCount').innerText = Math.floor(Math.random() * 1000);

    // Update time
    function updateTime() {
      const now = new Date();
      document.getElementById('time').innerText = now.toLocaleTimeString();
    }
    setInterval(updateTime, 1000);
    updateTime();