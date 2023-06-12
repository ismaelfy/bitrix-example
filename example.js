// custom.js
BX.ready(function () {
  // Suscribirse al canal de Push & Pull
  BX.addCustomEvent('onPullEvent', function (moduleId, command, params) {
    if (moduleId === 'pull' && command === 'logout') {
      // Realizar acciones necesarias al recibir el evento de cierre de sesión en otro navegador o sesión
      window.location.href = 'logout.php'; // Redirigir al usuario a la página de cierre de sesión
    }
  });

  // Suscribirse al canal
  BX.PULL.extendWatch('my_channel');
});


// # 2
// Verificar la cookie de sesión en JavaScript
var sessionId = getCookie('session_id');
if (sessionId && sessionId !== '<?php echo session_id(); ?>') {
  // Realizar acciones necesarias cuando se detecta una sesión activa en otro navegador o sesión
  window.location.href = 'logout.php'; // Redirigir al usuario a la página de cierre de sesión
}

// Función para obtener el valor de una cookie específica
function getCookie(name) {
  var cookies = document.cookie.split(';');
  for (var i = 0; i < cookies.length; i++) {
    var cookie = cookies[i].trim();
    if (cookie.indexOf(name + '=') === 0) {
      return cookie.substring(name.length + 1);
    }
  }
  return null;
}
