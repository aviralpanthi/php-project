<?php
require_once 'includes/Member.php';
$member = new Member();
$members = $member->getMembers();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members Tree</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Members Tree</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active"><a class="nav-link" href="#">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Contact Us</a></li>
            </ul>
        </div>
    </nav>
    <div class="container mt-4">
        <h1>Members Tree</h1>
        <ul id="members-list">
            <?php
            function displayMembers($members)
            {
                foreach ($members as $member) {
                    echo "<li>" . htmlspecialchars($member['Name']);
                    if (!empty($member['children'])) {
                        echo "<ul>";
                        displayMembers($member['children']);
                        echo "</ul>";
                    }
                    echo "</li>";
                }
            }
            displayMembers($members);
            ?>
        </ul>
        <button id="add-member-btn" class="btn btn-primary mt-3" data-toggle="modal" data-target="#exampleModal">Add
            Member</button>
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Member</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <label for="parent">Parent</label>
                                <select id="parent" name="parent" class="form-control">
                                    <option value="0">No Parent</option>
                                    <?php
                                    function displayMembersDropdown($members, $level = 0)
                                    {
                                        foreach ($members as $member) {
                                            $indent = str_repeat('', $level);
                                            echo "<option value='" . $member['Id'] . "'>" . $indent . htmlspecialchars($member['Name']) . "</option>";
                                            if (!empty($member['children'])) {
                                                displayMembersDropdown($member['children'], $level + 1);
                                            }
                                        }
                                    }
                                    displayMembersDropdown($members);
                                    ?>
                                </select>
                            </div>
                            <div class="col-sm-12 mt-2">
                                <label for="name">Name</label>
                                <input type="text" id="name" name="name" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" id="save-member-btn" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $('#add-member-btn').click(function () {
            $('#name').val('');
            $('#parent').val('0');
            $('#exampleModal').modal('show');
        });
        $(document).ready(function () {
            $('#add-member-btn').click(function () {
                $('#name').val('');
                $('#parent').val('0');
                $('#exampleModal').modal('show');
            });
            $('#save-member-btn').click(function () {
                var parent = $('#parent').val();
                var name = $('#name').val();

                if (name.trim() === "") {
                    alert("Name is required and must be a string.");
                    return;
                }

                $.ajax({
                    url: 'add_member.php',
                    type: 'POST',
                    data: {
                        parent: parent,
                        name: name
                    },
                    success: function (response) {
                        var data = JSON.parse(response);
                        if (data.success) {
                            var newMember = "<li>" + data.name + "</li>";
                            if (parent != 0) {
                                $('#members-list li').each(function () {
                                    if ($(this).text().trim() == data.parentName) {
                                        $(this).append("<ul>" + newMember + "</ul>");
                                    }
                                });
                            } else {
                                $('#members-list').append(newMember);
                            }

                            // Add the new member to the dropdown
                            var newOption = $("<option>").val(data.id).text(data.name);
                            if (parent != 0) {
                                $('#parent option[value="' + parent + '"]').after(newOption);
                            } else {
                                $('#parent').append(newOption);
                            }
                        }
                        $('#exampleModal').modal('hide');
                    }
                });
            });

        });
    </script>
</body>

</html>