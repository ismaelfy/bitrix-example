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
