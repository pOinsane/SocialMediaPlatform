function toggleNotifications() {

  var container = document.getElementById('notificationsContainer');
  if (container.style.display === 'block') {
    container.style.display = 'none';
  } else {
    container.style.display = 'block';
  }
}