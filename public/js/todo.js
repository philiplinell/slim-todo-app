$(document).ready(function() {
    $(".todo-item").on('click', 'input', function(event) {
        // Disable checkbox until we are done
        let checkbox = $(this);
        checkbox.prop('disabled', true);
        let spinner = checkbox.siblings('img');
        spinner.show();
        let todoItem = checkbox.closest('li');
        let todoID = todoItem.data('todo-id');
        let todoURL = todoItem.data('todo-link');
        // As in 'was done before we clicked the item'
        let wasDone = !$(this).is(":checked");
        $.ajax(todoURL, {
            type: 'POST',
            data: { 'todo_id': todoID },
            success: function(result) {
                if (wasDone) {
                    // Mark as not done
                    todoItem.removeClass("disabled");
                    todoItem.find('.todo-description').removeClass("item-done");
                    todoItem.data('todo-link', '/todos/setdone');
                    $(this).prop('checked', false);
                } else {
                    // Mark as done
                    todoItem.addClass("disabled");
                    todoItem.find('.todo-description').addClass("item-done");
                    todoItem.data('todo-link', '/todos/setundone');
                    $(this).prop('checked', true);
                }
                spinner.hide(500);
                checkbox.prop('disabled', false);
            }
        });
    });
});
