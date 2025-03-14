<?php
if ( ! is_user_logged_in() ) {
    wp_die( __( 'Войдите для доступа к проектам.', 'pmp' ) );
}
$project_id = isset( $_GET['pmp_project_id'] ) ? intval( $_GET['pmp_project_id'] ) : 0;
if ( ! $project_id ) {
    wp_die( __( 'Неверный идентификатор проекта.', 'pmp' ) );
}
global $wpdb;
$table = $wpdb->prefix . 'pmp_projects';
$project = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $project_id ) );
if ( ! $project ) {
    wp_die( __( 'Проект не найден', 'pmp' ) );
}
$current_user = get_current_user_id();
?>
<div class="pmp-frontend-project-page">
    <h2>Проект: <?php echo esc_html( $project->project_name ); ?></h2>
    <p><strong>Описание:</strong> <?php echo esc_html( $project->description ); ?></p>
    <p><strong>Срок:</strong> <?php echo esc_html( $project->deadline ); ?></p>
    <p><strong>Уровень срочности:</strong> <?php echo esc_html( $project->urgency_level ); ?></p>
    <p><strong>Статус:</strong> <?php echo esc_html( $project->status ); ?></p>
    
    <?php if ( $project->created_by != $current_user && $project->accepted == 0 ) : ?>
        <button id="pmp-accept-project-button" data-project="<?php echo $project->id; ?>">Принять проект</button>
        <div id="pmp-accept-project-response"></div>
    <?php endif; ?>
    
    <?php if ( current_user_can( 'pmp_assign_project' ) ) : ?>
        <button id="pmp-show-edit-project">Редактировать проект</button>
        <div id="pmp-edit-project-form" style="display:none; border:1px solid #ccc; padding:10px; margin-top:10px;">
            <form id="pmp-update-project-form">
                <input type="hidden" name="project_id" value="<?php echo $project->id; ?>" />
                <label for="upd_description">Описание:</label><br/>
                <textarea id="upd_description" name="description" style="width:100%;" required><?php echo esc_textarea( $project->description ); ?></textarea><br/><br/>
                
                <label for="upd_deadline">Срок:</label><br/>
                <input type="datetime-local" id="upd_deadline" name="deadline" style="width:100%;" value="<?php echo date('Y-m-d\TH:i', strtotime($project->deadline)); ?>" required /><br/><br/>
                
                <label for="upd_status">Статус:</label><br/>
                <select id="upd_status" name="status" style="width:100%;">
                    <option value="active" <?php selected( $project->status, 'active' ); ?>>Текущий</option>
                    <option value="future" <?php selected( $project->status, 'future' ); ?>>Пауза (Будущий)</option>
                    <option value="closed" <?php selected( $project->status, 'closed' ); ?>>Закрытый</option>
                </select><br/><br/>
                
                <?php wp_nonce_field( 'pmp_frontend_nonce', 'nonce' ); ?>
                <button type="submit">Сохранить изменения</button>
            </form>
            <div id="pmp-update-project-response"></div>
        </div>
        
        <div class="pmp-assign-project" style="margin-top:10px;">
            <h3>Назначить проект другому пользователю</h3>
            <form id="pmp-assign-project-form">
                <input type="hidden" name="project_id" value="<?php echo $project->id; ?>" />
                <label for="assign_user_id">Введите ID пользователя:</label>
                <input type="number" id="assign_user_id" name="user_id" required />
                <?php wp_nonce_field( 'pmp_frontend_nonce', 'assign_nonce' ); ?>
                <button type="submit">Назначить проект</button>
            </form>
            <div id="pmp-assign-project-response"></div>
        </div>
    <?php endif; ?>
    
    <!-- Плитка задач проекта -->
    <div class="pmp-tasks-list">
         <h3>Задачи проекта</h3>
         <div class="pmp-task-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(200px, 1fr)); gap:10px;">
         <?php
         $table_tasks = $wpdb->prefix . 'pmp_tasks';
         $tasks = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_tasks WHERE project_id = %d", $project_id ) );
         if ( $tasks ) {
             foreach ( $tasks as $task ) { ?>
                 <div class="pmp-task-card" style="border:1px solid #ccc; padding:10px;">
                     <h4><?php echo esc_html( $task->task_name ); ?></h4>
                     <p>Срочность: <?php echo esc_html( $task->urgency_level ); ?></p>
                     <p>Статус: <?php echo esc_html( $task->status ); ?></p>
                     <a href="<?php echo esc_url( add_query_arg( 'pmp_task_id', $task->id ) ); ?>">Подробнее</a>
                 </div>
             <?php }
         } else {
             echo '<p>Нет задач по этому проекту.</p>';
         }
         ?>
         </div>
    </div>
    
    <!-- Форма создания задачи -->
    <?php if ( current_user_can( 'pmp_manage_tasks' ) ) : ?>
        <div class="pmp-create-task" style="border:1px solid #ccc; padding:10px; margin-top:20px;">
            <h3>Создать новую задачу</h3>
            <form id="pmp-create-task-form">
                 <input type="hidden" name="project_id" value="<?php echo $project->id; ?>" />
                 
                 <label for="task_name">Название задачи:</label><br/>
                 <input type="text" id="task_name" name="task_name" style="width:100%;" required /><br/><br/>
                 
                 <label for="task_description">Описание задачи:</label><br/>
                 <textarea id="task_description" name="description" style="width:100%;" required></textarea><br/><br/>
                 
                 <label for="task_urgency_level">Уровень срочности:</label><br/>
                 <select id="task_urgency_level" name="urgency_level" style="width:100%;">
                      <option value="critical">Критический</option>
                      <option value="high">Высокий</option>
                      <option value="normal">Нормальный</option>
                 </select><br/><br/>
                 
                 <label for="executors">Назначьте исполнителей (ID через запятую):</label><br/>
                 <input type="text" id="executors" name="executors" style="width:100%;" /><br/><br/>
                 
                 <?php wp_nonce_field( 'pmp_frontend_nonce', 'nonce' ); ?>
                 <button type="submit">Создать задачу</button>
            </form>
            <div id="pmp-create-task-response"></div>
        </div>
    <?php endif; ?>
    
    <!-- Секция переписки по проекту -->
    <div class="pmp-project-messaging" style="margin-top:20px; border:1px solid #ccc; padding:10px;">
         <h3>Переписка по проекту</h3>
         <div id="pmp-project-messages">
             <?php
             $args = array(
                 'post_type'  => 'pmp_message',
                 'meta_query' => array(
                     array(
                         'key'   => 'object_type',
                         'value' => 'project',
                     ),
                     array(
                         'key'   => 'object_id',
                         'value' => $project->id,
                     )
                 )
             );
             $messages = get_posts( $args );
             if ( $messages ) {
                 foreach ( $messages as $msg ) {
                     echo '<div class="pmp-message" style="border-bottom:1px dashed #ccc; padding:5px 0;">';
                     echo '<p>' . esc_html( $msg->post_content ) . '</p>';
                     echo '<small>Дата: ' . esc_html( $msg->post_date ) . '</small>';
                     echo '</div>';
                 }
             } else {
                 echo '<p>Нет сообщений.</p>';
             }
             ?>
         </div>
         <form id="pmp-project-message-form">
             <input type="hidden" name="object_type" value="project" />
             <input type="hidden" name="object_id" value="<?php echo $project->id; ?>" />
             <?php wp_nonce_field( 'pmp_message_nonce', 'msg_nonce' ); ?>
             <textarea name="message" placeholder="Ваше сообщение" style="width:100%;" required></textarea><br/>
             <button type="submit">Отправить сообщение</button>
         </form>
         <div id="pmp-project-message-response"></div>
    </div>
    
    <p><a href="<?php echo esc_url( remove_query_arg( array( 'pmp_project_id', 'pmp_task_id' ) ) ); ?>">Вернуться к панели проектов</a></p>
</div>