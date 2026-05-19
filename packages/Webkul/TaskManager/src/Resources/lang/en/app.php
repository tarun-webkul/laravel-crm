<?php

return [

    'seeders' => [
        'attributes' => [
            'tasks' => [
                'title' => 'Title',
                'description' => 'Description',
                'status' => 'Status',
                'status-pending' => 'Pending',
                'status-in-progress' => 'In Progress',
                'status-on-hold' => 'On Hold',
                'status-completed' => 'Completed',
                'status-cancelled' => 'Cancelled',
                'priority' => 'Priority',
                'priority-low' => 'Low',
                'priority-medium' => 'Medium',
                'priority-high' => 'High',
                'priority-critical' => 'Critical',
                'deadline' => 'Deadline',
                'created_by' => 'Created By',
                'assigned_to' => 'Assigned To',
                'group' => 'Task Group',
            ],

            'task-groups' => [
                'name' => 'Name',
                'description' => 'Description',
                'created_by' => 'Owner',
            ],

            'task-comments' => [
                'comment' => 'Comment',
                'type' => 'Type',
            ],
        ],

        'attribute-options' => [
            'task-statuses' => [
                'pending' => 'Pending',
                'in-progress' => 'In Progress',
                'on-hold' => 'On Hold',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
            ],

            'task-priorities' => [
                'low' => 'Low',
                'medium' => 'Medium',
                'high' => 'High',
                'critical' => 'Critical',
            ],

            'task-comments' => [
                'internal' => 'Internal',
                'group' => 'Group',
            ],
        ],
    ],

    'acl' => [
        'task_manager' => 'Task Manager',
        'tasks' => 'Tasks',
        'task_groups' => 'Task Groups',
        'manage_members' => 'Manage Members',
        'manage_members_assign' => 'Assign',
        'manage_members_unassign' => 'Unassign',
        'create' => 'Create',
        'quick_add' => 'Quick Add',
        'edit' => 'Edit',
        'view' => 'View',
        'print' => 'Print',
        'delete' => 'Delete',
    ],

    'layouts' => [

        'task_manager' => 'Task Manager',
        'tasks' => 'Tasks',
        'task_groups' => 'Task Groups',
    ],

    'task_manager' => [

        'tasks' => [
            'index' => [
                'title' => 'Tasks',
                'create-btn' => 'Create Task',
                'create-success' => 'Task created successfully.',
                'update-success' => 'Task updated successfully.',
                'delete-success' => 'Task deleted successfully.',
                'delete-failed' => 'Task can not be deleted.',
                'datagrid' => [
                    'assigned-to' => 'Assigned To',
                    'delete' => 'Delete',
                    'edit' => 'Edit',
                    'id' => 'ID',
                    'title' => 'Title',
                    'task_group' => 'Task Group',
                    'created-by' => 'Owner',
                    'created-at' => 'Created At',
                    'priority' => 'Priority',
                    'status' => 'Status',
                    'subject' => 'Subject',
                    'view' => 'View',
                ],

            ],
            'create' => [
                'title' => 'Create Task',
                'save-btn' => 'Save Task',
            ],
            'edit' => [
                'title' => 'Edit Task',
                'save-btn' => 'Save Task',
            ],
            'view' => [
                'title' => ':title',
                'edit' => 'Edit',
                'details' => 'Details',
                'deadline' => 'Deadline',
                'priority' => 'Priority',
                'status' => 'Status',
                'assigned-to' => 'Assigned To',
                'created-by' => 'Owner',
                'created-at' => 'Created At',
                'description' => 'Description',
                'updated-at' => 'Updated At',
                'no_description' => 'No description provided.',
                'no-status' => 'No status provided.',
                'no-priority' => 'No priority provided.',
                'overdue' => 'Overdue',
            ],
        ],

        'task_groups' => [
            'index' => [
                'title' => 'Task Groups',
                'create-btn' => 'Create Task Group',
                'create-success' => 'Task Group created successfully.',
                'update-success' => 'Task Group updated successfully.',
                'delete-success' => 'Task Group deleted successfully.',
                'delete-failed' => 'Task Group can not be deleted.',
                'datagrid' => [
                    'assigned-to' => 'Assigned To',
                    'delete' => 'Delete',
                    'edit' => 'Edit',
                    'id' => 'ID',
                    'name' => 'Name',
                    'members-count' => 'Members Count',
                    'created-by' => 'Owner',
                    'created-at' => 'Created At',
                    'priority' => 'Priority',
                    'status' => 'Status',
                    'subject' => 'Subject',
                    'view' => 'View',
                ],
            ],
            'create' => [
                'title' => 'Create Task Group',
                'save-btn' => 'Save Task Group',
            ],
            'edit' => [
                'title' => 'Edit Task Group',
                'save-btn' => 'Save Task Group',
            ],
            'manage-members' => [
                'title' => 'Manage Members — :name',
                'header' => ':name — Manage Members',
                'description' => 'Add or remove users from this task group.',
                'assign-btn' => 'Assign Member',
                'assign-success' => 'Member assigned successfully.',
                'assign-failed' => 'Member can not be assigned.',
                'select-user' => 'Please select a user.',
                'unassign-success' => 'Member unassigned successfully.',
                'unassign-failed' => 'Member can not be unassigned.',
                'datagrid' => [
                    'id' => 'ID',
                    'name' => 'Name',
                    'email' => 'Email',
                    'created_at' => 'Created At',
                    'delete' => 'Delete',
                ],

            ],
        ],

        'task_comments' => [
            'index' => [
                'title' => 'Comments',
                'create-btn' => 'Add Comment',

                // UI strings used in resources/views/tasks/view.blade.php
                'post' => 'Post',
                'type' => 'Type',
                'comment' => 'Comment',
                'cancel' => 'Cancel',
                'save' => 'Save',
                'save-btn' => 'Save',

                'tab-group' => 'Group',
                'tab-internal' => 'Internal',
                'group' => 'Group',
                'internal' => 'Internal',

                'no-comments' => 'No comments yet.',

                // API / controller messages
                'create-success' => 'Comment added successfully.',
                'update-success' => 'Comment updated successfully.',
                'delete-success' => 'Comment deleted successfully.',
                'delete-failed' => 'Comment can not be deleted.',
                'edit-access-denied' => 'You do not have access to edit this comment.',
                'edit' => 'Edit',
                'delete-access-denied' => 'You do not have access to delete this comment.',
                'comment-access-denied' => 'You do not have access to comment on this task.',

                'internal_notes_restricted' => 'Only the task creator or assignee can post internal notes.',
                'comment_access_denied' => 'You do not have access to comment on this task.',

                'datagrid' => [
                    'comment' => 'Comment',
                    'created-by' => 'Created By',
                    'created-at' => 'Created At',
                    'delete' => 'Delete',
                ],
            ],
        ],
    ],

];
