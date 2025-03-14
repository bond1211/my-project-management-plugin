<?php
if ( ! current_user_can( 'pmp_manage' ) ) {
    wp_die( __( 'Доступ закрыт', 'pmp' ) );
}
?>
<div class="pmp-dashboard">
    <!-- Плавающая верхняя панель -->
    <div class="pmp-top-panel">
         <span>Непринятые задания: <span id="pmp-unaccepted-count">0</span></span>
         <span>Активные задания: <span id="pmp-active-count">0</span></span>
         <span>Просроченные задания: <span id="pmp-overdue-count">0</span></span>
    </div>
    <!-- Три колонки -->
    <div class="pmp-columns">
         <!-- Левая колонка (15%) -->
         <div class="pmp-column pmp-left" style="width:15%;">
              <h3>Закрытые проекты</h3>
              <input type="text" id="pmp-search-closed" placeholder="Поиск проекта" />
              <div id="pmp-closed-projects" style="overflow-y:auto; height: 300px;"></div>
         </div>
         <!-- Центральная колонка (70%) -->
         <div class="pmp-column pmp-central" style="width:70%;">
              <h3>Текущие проекты</h3>
              <div id="pmp-current-projects" class="pmp-card-grid"></div>
         </div>
         <!-- Правая колонка (15%) -->
         <div class="pmp-column pmp-right" style="width:15%;">
              <h3>Будущие проекты</h3>
              <input type="text" id="pmp-search-future" placeholder="Поиск проекта" />
              <div id="pmp-future-projects" style="overflow-y:auto; height: 300px;"></div>
         </div>
    </div>
</div>