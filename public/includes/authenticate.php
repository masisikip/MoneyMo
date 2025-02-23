<?php 
if (!empty($_SESSION['user_id']) && !empty($_SESSION['user_password'])) {
    $id = $_SESSION['user_id'];
    $password = $_SESSION['user_password'];
} else {
    header("Location: /public/login/");
}
global $user_type;

function authenticate($user_type) {
    ?>
    <script>
        fetch(`${window.location.origin}/api/authenticate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'x-id': <?php echo json_encode($_SESSION['user_id']); ?>,
                'x-password': <?php echo json_encode($_SESSION['user_password']); ?>,
                'x-type': <?php echo json_encode($user_type); ?>,
                'x-hashed': 'true'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Auth failed: ${response.statusText}`);
            }
            return response.text();
        })
        .then(data => {
            console.log('Authentication successful');
        })
        .catch(error => {
            alert(error.message);
        });
    </script>
    <?php
}
?>
