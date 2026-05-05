<?php
// views/partials/navbar_notificaciones.php

require_once __DIR__ . '/../../models/Notificacion.php';

$noLeidas = 0;
$alertasRecientes = [];

try {
    if (!isset($pdo) && class_exists('DB')) {
        $pdo = DB::getInstance()->getConnection();
    }

    if (isset($pdo)) {
        $notificacionModel = new Notificacion($pdo);
        $noLeidas = $notificacionModel->contarNoLeidas() ?? 0;
        $alertasRecientes = $notificacionModel->obtenerRecientes(8) ?? [];
    }
} catch (Exception $e) {
    error_log("Error en notificaciones: " . $e->getMessage());
}
?>

<style>
  .notif-wrapper { position: relative; display: inline-block; }
  .notif-btn { background: none; border: none; cursor: pointer; font-size: 1.4rem; padding: 6px 10px; position: relative; color: #fff; }
  .notif-badge { position: absolute; top: 2px; right: 4px; background: #e74c3c; color: #fff; font-size: 0.65rem; font-weight: 700; border-radius: 50%; min-width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; padding: 0 3px; line-height: 1; }
  .notif-dropdown { display: none; position: absolute; right: 0; top: calc(100% + 6px); width: 340px; background: #fff; border-radius: 10px; box-shadow: 0 8px 24px rgba(0,0,0,0.15); z-index: 9999; overflow: hidden; }
  .notif-dropdown.open { display: block; animation: fadeDown 0.18s ease; }
  @keyframes fadeDown { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
  .notif-header { background: #2c3e50; color: #fff; padding: 12px 16px; font-size: 0.85rem; font-weight: 600; display: flex; justify-content: space-between; align-items: center; }
  .notif-list { max-height: 320px; overflow-y: auto; color: #333; }
  .notif-item { display: flex; align-items: flex-start; gap: 10px; padding: 12px 16px; border-bottom: 1px solid #f0f0f0; transition: background 0.15s; text-decoration: none; position: relative; }
  .notif-item:hover { background: #fafafa; }
  .notif-body { flex: 1; }
  .notif-producto { font-size: 0.85rem; font-weight: 600; color: #2c3e50; margin: 0 0 2px; padding-right: 20px; }
  .notif-detalle { font-size: 0.76rem; color: #7f8c8d; margin: 0; }
  .notif-detalle span { color: #e74c3c; font-weight: 700; }
  .notif-empty { padding: 24px; text-align: center; color: #95a5a6; font-size: 0.85rem; }
  .notif-footer { padding: 10px 16px; text-align: center; font-size: 0.78rem; border-top: 1px solid #f0f0f0; color: #7f8c8d; background: #f9f9f9; }
</style>

<div class="notif-wrapper">
  <button class="notif-btn" id="btnCampana" title="Notificaciones de inventario">
    🔔
    <?php if ($noLeidas > 0): ?>
      <span class="notif-badge" id="notifBadge"><?= $noLeidas > 9 ? '9+' : $noLeidas ?></span>
    <?php endif; ?>
  </button>

  <div class="notif-dropdown" id="notifDropdown">
    <div class="notif-header">
      <span>⚠️ Alertas de Inventario</span>
      <span><?= (int)$noLeidas ?> sin leer</span>
    </div>

    <div class="notif-list">
      <?php if (empty($alertasRecientes)): ?>
        <div class="notif-empty">✅ Sin alertas de inventario</div>
      <?php else: ?>
        <?php foreach ($alertasRecientes as $alerta): ?>
          <div class="notif-item" id="notif-<?= $alerta['id_alerta'] ?>">
            <div class="notif-body">
              <div class="d-flex justify-content-between align-items-start">
                <p class="notif-producto"><?= htmlspecialchars($alerta['nombre_prod']) ?></p>
                
                <!-- BOTÓN CHECK (USA id_alerta) -->
                <button onclick="cambiarEstadoNotif(<?= $alerta['id_alerta'] ?>)" 
                        class="btn btn-outline-success btn-sm p-0 px-1" title="Marcar como resuelto">
                    ✅
                </button>
              </div>
              
              <p class="notif-detalle">Stock actual: <span><?= $alerta['stock_actual'] ?></span></p>

              <!-- BOTÓN DE PROVEEDOR (si existe id_provider) -->
              <?php if (!empty($alerta['id_provider'])): ?>
                <button class="btn btn-dark btn-sm mt-2 w-100" style="font-size: 10px;" 
                        onclick="mostrarInfoProveedor('<?= addslashes($alerta['nombre_empresa']) ?>', '<?= addslashes($alerta['nombre_contacto']) ?>', '<?= $alerta['telefono'] ?>')">
                    📦 Ver Proveedor
                </button>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
    <div class="notif-footer">Gualo Electronic · Observer System</div>
  </div>
</div>

<script>
// Evitar conflictos con otros scripts usando un scope cerrado
(function() {
  const btn = document.getElementById('btnCampana');
  const dropdown = document.getElementById('notifDropdown');

  if (btn && dropdown) {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      dropdown.classList.toggle('open');
    });
    document.addEventListener('click', () => dropdown.classList.remove('open'));
    dropdown.addEventListener('click', (e) => e.stopPropagation());
  }
})();

function cambiarEstadoNotif(id) {
    if(!confirm('¿Marcar como resuelta?')) return;
    
    // Cambiamos a ruta relativa para evitar problemas con el nombre de la carpeta raíz
    fetch('../actions/desactivar_notificacion.php?id=' + id)
    .then(r => {
        if (!r.ok) throw new Error('Error en la respuesta del servidor');
        return r.json();
    })
    .then(data => {
        if(data.ok) {
            const el = document.getElementById('notif-' + id);
            if(el) {
                el.style.transition = '0.3s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 300);
            }
            
            // Opcional: Actualizar el contador del badge
            const badge = document.getElementById('notifBadge');
            if (badge) {
                let count = parseInt(badge.innerText);
                if (count <= 1) badge.remove();
                else badge.innerText = count - 1;
            }
        } else {
            alert("Error: " + data.error);
        }
    })
    .catch(err => {
        console.error("Fallo el fetch:", err);
        alert("No se pudo conectar con el servidor.");
    });
}

function mostrarInfoProveedor(empresa, contacto, tel) {
    alert("DETALLES DEL PROVEEDOR\n\n🏢 Empresa: " + empresa + "\n👤 Contacto: " + contacto + "\n📞 Teléfono: " + tel);
}
</script>