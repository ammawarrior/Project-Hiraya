<?php 
require_once 'layout/header.php';
require_once 'config/database.php';

// Initialize session and CSRF token
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Please log in to view your account settings.";
    header("Location: login.php");
    exit;
}

// Initialize user data from session
$user_picture = isset($_SESSION['user_picture']) ? 'uploads/' . $_SESSION['user_picture'] : 'img/profile_null.png';
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

// Fetch user data from database
try {
    $stmt = $pdo->prepare("SELECT email, created_at, last_password_change FROM users WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$userData) {
        throw new Exception("User data not found");
    }
    
    // Update session data
    $_SESSION['email'] = $userData['email'] ?? '';
    $email = $userData['email'] ?? '';
    
    // Handle null timestamps
    $_SESSION['created_at'] = $userData['created_at'] ?? null;
    $_SESSION['last_password_change'] = $userData['last_password_change'] ?? null;
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $_SESSION['error_message'] = "Failed to load account information. Please try again later.";
    header("Location: account-settings.php");
    exit;
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    $_SESSION['error_message'] = "Failed to load account information. Please try again later.";
    header("Location: account-settings.php");
    exit;
}
?>

<!-- Add UIkit notification JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show success message if exists
    <?php if (isset($_SESSION['success_message'])): ?>
        UIkit.notification({
            message: '<?php echo addslashes($_SESSION['success_message']); ?>',
            status: 'success',
            timeout: 3000,
            pos: 'top-center'
        });
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    // Show error message if exists
    <?php if (isset($_SESSION['error_message'])): ?>
        UIkit.notification({
            message: '<?php echo addslashes($_SESSION['error_message']); ?>',
            status: 'danger',
            timeout: 5000,
            pos: 'top-center'
        });
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
});
</script>

<div class="uk-section uk-section-default">
    <div class="uk-container">
        <div class="uk-card uk-card-default uk-card-body uk-box-shadow-xlarge">
            <div class="uk-grid-medium uk-child-width-1-2@m uk-child-width-1-1@s" data-uk-grid>
                <!-- Left Column - Account Information and Profile -->
                <div>
                    <div class="uk-card uk-card-default uk-card-body uk-margin">
                        <h3 class="uk-card-title uk-text-bold uk-text-success">Account Information</h3>
                        <form action="update-account.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            
                            <div class="uk-margin">
                                <label class="uk-form-label">Username</label>
                                <div class="uk-form-controls">
                                    <div class="uk-text-bold uk-text-success">
                                        <?php echo htmlspecialchars($username); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="uk-margin">
                                <label class="uk-form-label">Email</label>
                                <div class="uk-form-controls">
                                    <div class="uk-text-bold uk-text-success">
                                        <?php echo htmlspecialchars($email); ?>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="uk-card uk-card-default uk-card-body uk-margin">
                        <h3 class="uk-card-title uk-text-bold uk-text-success">Profile Picture</h3>
                        <form action="update-profile-picture.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            
                            <div class="uk-margin">
                                <label class="uk-form-label" for="user_picture">Current Picture</label>
                                <div class="uk-form-controls">
                                    <div class="uk-margin">
                                        <img id="profile-preview" src="<?php echo htmlspecialchars($user_picture); ?>" 
                                             alt="Profile Picture" 
                                             class="uk-border-circle uk-align-center" 
                                             width="150" 
                                             height="150">
                                    </div>
                                </div>
                            </div>

                            <div class="uk-margin">
                                <label class="uk-form-label" for="user_picture">Upload New Picture</label>
                                <div class="uk-form-controls">
                                    <div class="uk-margin">
                                        <input class="uk-input uk-form-width-medium" 
                                               type="file" 
                                               id="user_picture" 
                                               name="user_picture" 
                                               accept="image/jpeg,image/png,image/webp">
                                    </div>
                                    <div class="uk-margin">
                                        <button class="uk-button uk-button-primary uk-width-1-1" type="submit">Update Picture</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Right Column - Account Security -->
                <div>
                    <div class="uk-card uk-card-default uk-card-body uk-margin">
                        <h3 class="uk-card-title uk-text-bold uk-text-success">Account Security</h3>
                        
                        <div class="uk-margin">
                            <p class="uk-text-meta">Last password change: <?php 
                                echo isset($_SESSION['last_password_change']) && $_SESSION['last_password_change'] !== null 
                                    ? date('F j, Y', strtotime($_SESSION['last_password_change']))
                                    : 'Not yet changed';
                            ?></p>
                        </div>

                        <div class="uk-margin">
                            <p class="uk-text-meta">Account created: <?php 
                                echo isset($_SESSION['created_at']) && $_SESSION['created_at'] !== null 
                                    ? date('F j, Y', strtotime($_SESSION['created_at']))
                                    : 'Unknown';
                            ?></p>
                        </div>

                        <div class="uk-margin">
                            <button class="uk-button uk-button-default uk-width-1-1" 
                                    type="button" 
                                    uk-toggle="target: #change-password-modal">
                                Change Password
                            </button>
                        </div>

                        <div class="uk-margin">
                            <button class="uk-button uk-button-danger uk-width-1-1" 
                                    type="button" 
                                    onclick="confirmDeleteAccount()">
                                Delete Account
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div id="change-password-modal" uk-modal>
    <div class="uk-modal-dialog uk-modal-dialog-large uk-modal-body">
        <h3 class="uk-modal-title uk-text-center">Change Password</h3>
        <form action="update-account.php" method="POST" enctype="multipart/form-data" class="uk-form-stacked">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <div class="uk-margin">
                <label class="uk-form-label">Current Password</label>
                <div class="uk-form-controls">
                    <input class="uk-input" type="password" name="current_password_modal" required>
                </div>
            </div>

            <div class="uk-margin">
                <label class="uk-form-label">New Password</label>
                <div class="uk-form-controls">
                    <input class="uk-input" type="password" name="new_password_modal" required>
                </div>
            </div>

            <div class="uk-margin">
                <label class="uk-form-label">Confirm New Password</label>
                <div class="uk-form-controls">
                    <input class="uk-input" type="password" name="confirm_password_modal" required>
                </div>
            </div>

            <div class="uk-margin">
                <button class="uk-button uk-button-primary uk-width-1-1" type="submit">Update Password</button>
            </div>
        </form>
    </div>
</div>

<!-- Add SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Initialize SweetAlert2
const Swal = window.Swal;

function validateImage(input) {
    const file = input.files[0];
    if (file) {
        const maxSize = 5 * 1024 * 1024; // 5MB
        if (file.size > maxSize) {
            UIkit.notification({
                message: 'Image size must be less than 5MB',
                status: 'danger',
                timeout: 3000,
                pos: 'top-center'
            });
            input.value = '';
            return false;
        }
        
        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            UIkit.notification({
                message: 'Only JPEG, PNG, and WebP images are allowed',
                status: 'danger',
                timeout: 3000,
                pos: 'top-center'
            });
            input.value = '';
            return false;
        }
    }
    return true;
}

function confirmDeleteAccount() {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to recover your account!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete account'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'delete-account.php';
        }
    });
}

// Handle profile picture preview
const profilePictureInput = document.getElementById('user_picture');
const profilePicturePreview = document.getElementById('profile-preview');

if (profilePictureInput && profilePicturePreview) {
    profilePictureInput.addEventListener('change', function(e) {
        const file = this.files[0];
        if (file) {
            // Validate file before preview
            if (!validateImage(this)) {
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                profilePicturePreview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
}

// Initialize UIkit and modal
UIkit.util.ready(function () {
    // Password modal form validation
    const passwordModal = UIkit.modal('#change-password-modal');
    const passwordForm = passwordModal.element.querySelector('form');
    
    if (passwordForm) {
        passwordForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const newPassword = document.querySelector('input[name="new_password_modal"]').value;
            const confirmPassword = document.querySelector('input[name="confirm_password_modal"]').value;
            const currentPassword = document.querySelector('input[name="current_password_modal"]').value;
            const csrfToken = document.querySelector('input[name="csrf_token"]').value;
            
            // Validate password match
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'New passwords do not match!',
                    timer: 3000
                });
                return;
            }

            try {
                const formData = new FormData();
                formData.append('current_password_modal', currentPassword);
                formData.append('new_password_modal', newPassword);
                formData.append('confirm_password_modal', confirmPassword);
                formData.append('csrf_token', csrfToken);

                const response = await fetch('update-account.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Password changed successfully!',
                        timer: 3000
                    }).then(() => {
                        // Close modal and refresh page
                        passwordModal.hide();
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: result.message || 'Failed to change password',
                        timer: 3000
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while changing password',
                    timer: 3000
                });
            }
        });
    }
});
</script>

<?php require_once 'layout/footer.php'; ?>
