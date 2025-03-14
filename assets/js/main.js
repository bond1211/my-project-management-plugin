jQuery(document).ready(function($){
    // Создание проекта
    $('#pmp-create-project-form').on('submit', function(e){
         e.preventDefault();
         var formData = $(this).serialize() + '&action=pmp_create_project';
         $.ajax({
              url: pmp_frontend_obj.ajaxurl,
              method: 'POST',
              data: formData,
              success: function(response) {
                   if(response.success) {
                        $('#pmp-create-project-response').html('<p>' + response.data + '</p>');
                        location.reload();
                   } else {
                        $('#pmp-create-project-response').html('<p>' + response.data + '</p>');
                   }
              },
              error: function(jqXHR, textStatus, errorThrown) {
                  console.log("Error: "+errorThrown);
              }
         });
    });
    
    // Обновление проекта
    $('#pmp-update-project-form').on('submit', function(e){
         e.preventDefault();
         var formData = $(this).serialize() + '&action=pmp_update_project';
         $.ajax({
              url: pmp_frontend_obj.ajaxurl,
              method: 'POST',
              data: formData,
              success: function(response) {
                   $('#pmp-update-project-response').html('<p>' + response.data + '</p>');
              }
         });
    });
    
    // Назначение проекта
    $('#pmp-assign-project-form').on('submit', function(e){
         e.preventDefault();
         var formData = $(this).serialize() + '&action=pmp_assign_project';
         $.ajax({
              url: pmp_frontend_obj.ajaxurl,
              method: 'POST',
              data: formData,
              success: function(response) {
                   $('#pmp-assign-project-response').html('<p>' + response.data + '</p>');
              }
         });
    });
    
    // Принятие проекта
    $('#pmp-accept-project-button').on('click', function(){
         var projectID = $(this).data('project');
         var data = {
              action: 'pmp_accept_project',
              project_id: projectID,
              nonce: pmp_frontend_obj.nonce
         };
         $.post(pmp_frontend_obj.ajaxurl, data, function(response){
              $('#pmp-accept-project-response').html('<p>' + response.data + '</p>');
              if(response.success){
                   location.reload();
              }
         });
    });
    
    // Создание задачи
    $('#pmp-create-task-form').on('submit', function(e){
         e.preventDefault();
         var formData = $(this).serialize() + '&action=pmp_create_task';
         $.ajax({
              url: pmp_frontend_obj.ajaxurl,
              method: 'POST',
              data: formData,
              success: function(response) {
                   if(response.success) {
                        $('#pmp-create-task-response').html('<p>' + response.data + '</p>');
                        location.reload();
                   } else {
                        $('#pmp-create-task-response').html('<p>' + response.data + '</p>');
                   }
              }
         });
    });
    
    // Закрытие задачи
    $('#pmp-close-task-form').on('submit', function(e){
         e.preventDefault();
         var formData = $(this).serialize() + '&action=pmp_close_task';
         $.ajax({
              url: pmp_frontend_obj.ajaxurl,
              method: 'POST',
              data: formData,
              success: function(response) {
                   $('#pmp-close-task-response').html('<p>' + response.data + '</p>');
                   if(response.success){
                      location.reload();
                   }
              }
         });
    });
    
    // Отправка сообщения по проекту
    $('#pmp-project-message-form').on('submit', function(e){
         e.preventDefault();
         var formData = $(this).serialize() + '&action=pmp_send_message';
         $.ajax({
              url: pmp_frontend_obj.ajaxurl,
              method: 'POST',
              data: formData,
              success: function(response) {
                   if(response.success) {
                        $('#pmp-project-message-response').html('<p>Сообщение отправлено</p>');
                        location.reload();
                   } else {
                        $('#pmp-project-message-response').html('<p>' + response.data + '</p>');
                   }
              }
         });
    });
    
    // Отправка сообщения по задаче
    $('#pmp-task-message-form').on('submit', function(e){
         e.preventDefault();
         var formData = $(this).serialize() + '&action=pmp_send_message';
         $.ajax({
              url: pmp_frontend_obj.ajaxurl,
              method: 'POST',
              data: formData,
              success: function(response) {
                   if(response.success) {
                        $('#pmp-task-message-response').html('<p>Сообщение отправлено</p>');
                        location.reload();
                   } else {
                        $('#pmp-task-message-response').html('<p>' + response.data + '</p>');
                   }
              }
         });
    });
    
    // Переключение формы редактирования проекта
    $('#pmp-show-edit-project').on('click', function(){
         $('#pmp-edit-project-form').toggle();
    });
});