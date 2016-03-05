<!-- Registration Modal -->
<div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="registerModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" data-target="#registerModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="registerModalLabel">Create a new login</h4>
            </div>
            <form id="registrationForm">
                <div class="modal-body">
                    <div class="alert alert-danger" role="alert" id="ajaxErrorMessage" style="display:none;">An error occurred on the server</div>
                    <div id="registerUsernameGroup" class="form-group">
                        <label for="registerUsername">Username</label>
                        <input type="text" id="registerUsername" class="form-control" placeholder="Username" />
                        <span id="registerUsernameGlyph" class="glyphicon form-control-feedback" aria-hidden="true"></span>
                    </div>
                    <div class="alert alert-danger" role="alert" id="usernameErrorMessage" style="display:none;">Username is unavailable</div>

                    <div id="registerPasswordGroup1" class="form-group">
                        <label for="registerPassword1">Password</label>
                        <input type="password" id="registerPassword1" class="form-control" placeholder="Password" />
                        <span id="registerPassword1Glyph" class="glyphicon form-control-feedback" aria-hidden="true"></span>
                    </div>
                    <div id="registerPasswordGroup2" class="form-group">
                        <label for="registerPassword2">Password (again)</label>
                        <input type="password" id="registerPassword2" class="form-control" placeholder="Password" />
                        <span id="registerPassword2Glyph"  class="glyphicon form-control-feedback" aria-hidden="true"></span>
                    </div>
                    <div class="alert alert-danger" role="alert" id="passwordErrorMessage" style="display:none;">Passwords do not match or are too short</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" data-target="#registerModal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="registerUser">Register</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    /**
     * Check the password fields for registration and see if they match, if they don't add
     * an error state to the registration form
     */
    function warnPasswords() {
        var pass1 = $('#registerPassword1').val();
        var pass2 = $('#registerPassword2').val();
        if (pass1.length > 5 && pass1 == pass2) {
            $("#registerPasswordGroup1").removeClass('has-error').addClass('has-success');
            $("#registerPasswordGroup2").removeClass('has-error').addClass('has-success');
            $('#registerPassword1Glyph').removeClass('glyphicon-remove').addClass('glyphicon-ok');
            $('#registerPassword2Glyph').removeClass('glyphicon-remove').addClass('glyphicon-ok');
            $("#passwordErrorMessage").hide();
        } else {
            $("#registerPasswordGroup1").addClass('has-error has-feedback');
            $("#registerPasswordGroup2").addClass('has-error has-feedback');
            $('#registerPassword1Glyph').addClass('glyphicon-remove').removeClass('glyphicon-ok');
            $('#registerPassword2Glyph').addClass('glyphicon-remove').removeClass('glyphicon-ok');
            $("#passwordErrorMessage").show();
        }
    }

    /**
     * Part of registration, sees if the currently entered username is available
     */
    function checkUsername() {

        // Reset any error/information
        $('#registerUsernameGroup').removeClass('has-feedback').removeClass('has-success').removeClass('has-error');
        $('#registerUsernameGlyph').removeClass('glyphicon-ok').removeClass('glyphicon-remove');

        // Get the current value of username
        var username = $("#registerUsername").val();

        // send the request to the server
        $.ajax({
            accepts: 'application/json',
            cache: false,
            data: {'username': username, method:'isAvailable'},
            dataType: "json",
            error: function() {
                // If the server returns an error code, the name is unavailable
                $('#registerUsernameGlyph').addClass('glyphicon-remove');
                $('#registerUsernameGroup').addClass('has-error has-feedback');
                $('#usernameErrorMessage').show();
            },
            method: 'POST',
            success: function() {
                // If the name is available, indicate the field has been filled out successfully
                $('#registerUsernameGlyph').addClass('glyphicon-ok');
                $('#registerUsernameGroup').addClass('has-success has-feedback');
                $('#usernameErrorMessage').hide();
            },
            url: 'api/user.php'
        });
    }

    /**
     * Registers the user for a new account
     */
    function registerUser() {

        // hide the network error message if it's visible
        $('#ajaxErrorMessage').hide();

        // make sure the form checks out okay
        checkUsername();
        warnPasswords();

        // if the form has an error, stop here
        if ($("#registerUsernameGroup").hasClass('has-error') ||
            $("#registerPasswordGroup1").hasClass('has-error')) {
            return;
        }

        // get the username and one of the passwords
        var username = $("#registerUsername").val();
        var password = $("#registerPassword1").val();

        // send the register request to the server
        $.ajax({
            accepts: 'application/json',
            cache: false,
            data: {'username': username, 'password': password, 'method': 'addUser'},
            dataType: "json",
            error: function() {
                // display an error message
                $('#ajaxErrorMessage').show();
            },
            method: 'POST',
            statusCode: {
                201: function() {
                    login(username, password);
                }
            },
            success: function() { login(username, password); },
            url: 'api/user.php'
        });
    }

    // Setup event handlers etc
    $(document).ready(function(){
        // Handle checking the passwords match
        $("#registerPassword1").on('focusout', warnPasswords);
        $("#registerPassword2").on('focusout', warnPasswords);

        $("#registerUsername").keyup(checkUsername);

        // Handle submitting the registration form
        $("#registerUser").on( "click", registerUser);
    });
</script>
