<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Form</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 20px;
            font-weight: bold;
            margin: 20px;
        }

        h1 {
            color: #333;
            text-align: center;
        }

        h2 {
            color: #666;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        button {
            padding: 10px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .edit-btn, .delete-btn {
            padding: 5px;
            margin-right: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>CRUD OPERATIONS</h1>
    
    <form id="userForm">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required><br>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required><br>

        <button type="button" onclick="insertUser($('#username').val(), $('#email').val(), $('#password').val())">Insert User</button>
    </form>

    <h2>User Records</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Operation</th>
            </tr>
        </thead>
        <tbody id="userTableBody">
        </tbody>
    </table>

    <script>
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
                loadUserTable();
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
                success: function (data) {
                    alert('User added successfully!');
                    loadUserTable();
                    resetForm();
                },
                error: function () {
                    alert('Failed to add user!');
                }
            });
        }

        function updateUser(userId, username, email, password) {
            if (checkExistingUser(username, email, userId)) {
                alert('Username or email already exists!');
                loadUserTable();
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
                success: function (data) {
                    alert('User updated successfully!');
                    loadUserTable();
                    resetForm();
                },
                error: function () {
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
                success: function (data) {
                    $.each(data, function (index, user) {
                        if ((user.username === username || user.email === email) && user.id != userId) {
                            exists = true;
                            return false;
                        }
                    });
                },
                error: function () {
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

        function loadUserTable() {
            $.ajax({
                type: "post",
                url: "<?= base_url() . 'usercontroller/getAllUsers' ?>",
                dataType: "json",
                success: function(data) {
                    $('#userTableBody').empty();
                    $.each(data, function(index, user) {
                        var row = '<tr><td>' + user.username + '</td><td>' + user.email + '</td>' +
                                '<td>' +
                                '<button class="edit-btn" onclick="editUser(' + user.id + ')">Edit</button>' +
                                ' <button class="delete-btn" onclick="deleteUser(' + user.id + ')">Delete</button>' +
                                '</td></tr>';
                        $('#userTableBody').append(row);
                    });
                },
                error: function() {
                    alert('Failed to load user records!');
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
                        loadUserTable();
                    } else {
                        alert('Failed to delete user!');
                    }
                },
                error: function() {
                    alert('Failed to delete user!');
                }
            });
        }
        loadUserTable();
    </script>
</body>
</html>
