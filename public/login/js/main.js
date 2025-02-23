$(document).ready(function() {

  async function hashPassword(password) {
    const encoder = new TextEncoder();
    const data = encoder.encode(password);
    const hashBuffer = await crypto.subtle.digest('SHA-256', data);
    return Array.from(new Uint8Array(hashBuffer))
      .map(byte => byte.toString(16).padStart(2, '0'))
      .join('');
  }
    
  $('#loginForm').submit(async function(event) {
    event.preventDefault();

    const email = $('#email').val();
    const password = $('#password').val();

    if (!email || !password) {
      alert('Email and password are required!');
      return;
    }

    const hashedPassword = await hashPassword(password);

    $.ajax({
      url: `${window.location.origin}/api/login/`,
      type: 'POST',
      contentType: 'application/x-www-form-urlencoded',
      headers: {
        'x-email': email,
        'x-password': hashedPassword
      },
      success: function(response) {
        window.location.href = '/public/test-page'
      },
      error: function(xhr) {
        alert(`Login failed: ${xhr.responseText}`);
      }
    });
  });
});
