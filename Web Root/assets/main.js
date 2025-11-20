function showAlert(message, type='success') {
    const container = document.getElementById('alert-container');
    if(!container) return;
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.innerHTML = `${type==='success'?'✓':'⚠'} ${message}`;
    container.appendChild(alert);
    setTimeout(() => alert.remove(), 3000);
}
