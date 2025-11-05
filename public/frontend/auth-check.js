// Check authentication status when page loads
document.addEventListener('DOMContentLoaded', async () => {
    try {
        const response = await fetch('../api/check_auth_status.php');
        const data = await response.json();
        
        if (!data.isAuthenticated && data.redirectUrl) {
            window.location.href = data.redirectUrl;
        }
    } catch (error) {
        console.error('Authentication check failed:', error);
        // Optionally redirect to login page on error
        window.location.href = 'acceuil.html';
    }
});