<?php
if ( ! current_user_can( 'pmp_manage' ) ) {
    wp_die( __( 'Доступ закрыт', 'pmp' ) );
}
$project_id = isset( $_GET['project_id'] ) ? intval( $_GET['project_id'] ) : 0;
?>
<div class="pmp-project-page">
    <h2>Страница проекта <?php echo $project_id; ?></h2>
    <div class="pmp-task-list">
         <h3>Текущие задания</h3>
         <ul id="pmp-current-tasks">
             <!-- Динамически подгружаются задания -->
         </ul>
         <h3>Завершённые задания</h3>
         <ul id="pmp-completed-tasks">
             <!-- Динамически подгружаются задания -->
         </ul>
    </div>
    <div class="pmp-messaging">
         <h3>Сообщения</h3>
         <div id="pmp-messages-history">
             <!-- История сообщений -->
         </div>
         <form id="pmp-message-form">
              <textarea name="message" id="pmp_message" placeholder="Введите сообщение"></textarea>
              <input type="hidden" name="project_id" value="<?php echo $project_id; ?>" />
              <?php wp_nonce_field( 'pmp_message_nonce', 'nonce' ); ?>
              <button type="submit">Отправить сообщение</button>
         </form>
    </div>
</div>