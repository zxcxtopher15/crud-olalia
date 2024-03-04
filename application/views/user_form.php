<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Form</title>
    <!-- Add missing CSS for DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.2/css/dataTables.bootstrap4.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-4">
        <h1 class="text-center">CRUD OPERATIONS</h1>

        <form id="userForm">
            <!-- Add a hidden input field for user ID -->
            <input type="hidden" id="userId" name="userId">

            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" name="username" id="username" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" name="email" id="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" name="password" id="password" required>
            </div>

            <button type="button" class="btn btn-primary" onclick="insertOrUpdateUser()">Insert User</button>
        </form>

        <h2 class="mt-4">User Records</h2>
        <table class="table table-bordered table-hover" id="userTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Operations</th>
                </tr>
            </thead>
            <tbody>
                <?php $cnt = 1; ?>
                <?php foreach ($record as $row) : ?>
                    <tr>
                        <td><?php echo $cnt; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                    </tr>
                    <?php $cnt++; ?>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.2/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#userTable').DataTable({
                "ajax": {
                    "url": "<?= base_url() . 'usercontroller/getAllUsers' ?>",
                    "type": "POST",
                    "dataType": "json",
                    "dataSrc": ""
                },
                "columns": [{
                        "data": "id"
                    },
                    {
                        "data": "username"
                    },
                    {
                        "data": "email"
                    },
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            return '<button class="btn btn-sm btn-primary" onclick="editUser(' + row.id + ')">Edit</button>' +
                                '<button class="btn btn-sm btn-danger" onclick="deleteUser(' + row.id + ')">Delete</button>';
                        }
                    }
                ]
            });
        });


        var editingUserId = null;

        function insertOrUpdateUser() {
            var username = $('#username').val();
            var email = $('#email').val();
            var password = $('#password').val();

            if (username.trim() === '' || email.trim() === '' || password.trim() === '') {
                alert('Please fill in all the fields.');
                return;
            }

            if (editingUserId) {
                updateUser(editingUserId, username, email, password);
            } else {
                insertUser(username, email, password);
            }
        }

        function insertUser(username, email, password) {
            if (checkExistingUser(username, email)) {
                alert('Username or email already exists!');
                resetForm();
                return;
            }

            $.ajax({
                type: "post",
                url: "<?= base_url() . 'usercontroller/insertUser' ?>",
                data: {
                    'username': username,
                    'email': email,
                    'password': password
                },
                dataType: "json",
                success: function(data) {
                    alert('User added successfully!');
                    $('#userTable').DataTable().ajax.reload();
                    resetForm();
                },
                error: function() {
                    alert('Failed to add user!');
                }
            });
        }

        function updateUser(userId, username, email, password) {
            if (checkExistingUser(username, email, userId)) {
                alert('Username or email already exists!');
                $('#userTable').DataTable().ajax.reload();
                resetForm();
                return;
            }

            var confirmUpdate = confirm('Are you sure you want to update User ID ' + userId + '?');
            if (!confirmUpdate) {
                return;
            }

            $.ajax({
                type: "post",
                url: "<?= base_url() . 'usercontroller/updateUser/' ?>" + userId,
                data: {
                    'username': username,
                    'email': email,
                    'password': password
                },
                dataType: "json",
                success: function(data) {
                    alert('User updated successfully!');
                    $('#userTable').DataTable().ajax.reload();
                    resetForm();
                },
                error: function() {
                    alert('Failed to update user!');
                }
            });
        }

        function checkExistingUser(username, email, userId) {
            var exists = false;

            $.ajax({
                type: "post",
                async: false,
                url: "<?= base_url() . 'usercontroller/getAllUsers' ?>",
                dataType: "json",
                success: function(data) {
                    $.each(data, function(index, user) {
                        if ((user.username === username || user.email === email) && user.id != userId) {
                            exists = true;
                            return false;
                        }
                    });
                },
                error: function() {
                    alert('Failed to check existing users!');
                }
            });

            return exists;
        }

        function editUser(userId) {
            editingUserId = userId;

            $.ajax({
                type: "post",
                url: "<?= base_url() . 'usercontroller/editUser/' ?>" + userId,
                dataType: "json",
                success: function(data) {
                    $('#username').val(data.username);
                    $('#email').val(data.email);
                    $('button').text('Update User');
                    $('button').attr('onclick', 'insertOrUpdateUser()');


                },
                error: function() {
                    alert('Failed to fetch user details for editing!');
                }
            });
        }

        function editUser(userId) {
            editingUserId = userId;

            $.ajax({
                type: "post",
                url: "<?= base_url() . 'usercontroller/editUser/' ?>" + userId,
                dataType: "json",
                success: function(data) {
                    $('#username').val(data.username);
                    $('#email').val(data.email);
                    $('button').text('Update User');
                    $('button').attr('onclick', 'insertOrUpdateUser()');
                    $('.edit-btn').hide();
                    $('.delete-btn').hide();
                    $('th:contains("Operation"), td:nth-child(4)').hide();
                },
                error: function() {
                    alert('Failed to fetch user details!');
                }
            });
        }

        function resetForm() {
            $('#username').val('');
            $('#email').val('');
            $('#password').val('');

            $('button').text('Insert User');
            $('button').attr('onclick', 'insertOrUpdateUser()');
            $('th:contains("Operation"), td:nth-child(4)').show();

            editingUserId = null;
        }

        function deleteUser(userId) {
            var confirmDelete = confirm('Are you sure you want to delete User ID ' + userId + '?');
            if (!confirmDelete) {
                return;
            }

            $.ajax({
                type: "post",
                url: "<?= base_url() . 'usercontroller/deleteUser/' ?>" + userId,
                dataType: "json",
                success: function(data) {
                    if (data.success) {
                        alert('User deleted successfully!');
                        $('#userTable').DataTable().ajax.reload();
                    } else {
                        alert('Failed to delete user!');
                    }
                },
                error: function() {
                    alert('Failed to delete user!');
                }
            });
        }
    </script>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>