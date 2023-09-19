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





let field = {
  items: [],

  add: function(key, value) {
    this.items.push({ key: key, value: value });
    this.updateSortable();
  },

  remove: function(key) {
    this.items = this.items.filter(item => item.key !== key);
    this.updateSortable();
  },

  get: function(key) {
    return this.items.find(item => item.key === key);
  },

  all: function() {
    return this.items;
  },

  updateSortable: function() {
    const list = document.getElementById('sortable-list');
    list.innerHTML = '';
    this.items.forEach(item => {
      const li = document.createElement('li');
      li.innerText = `${item.key}: ${item.value}`;
      list.appendChild(li);
    });

    if (this.sortable) {
      this.sortable.destroy();
    }

    this.sortable = new Sortable(list, {
      animation: 150,
      ghostClass: 'sortable-ghost',
      onSort: function(evt) {
        const fromIndex = evt.oldIndex;
        const toIndex = evt.newIndex;

        const movedItem = field.items.splice(fromIndex, 1)[0];
        field.items.splice(toIndex, 0, movedItem);
      }
    });
  }
};

// Ejemplo de uso
field.add("nombre", "John Doe");
field.add("edad", 30);

// Agrega un contenedor HTML para la lista ordenable
const sortableContainer = document.createElement('ul');
sortableContainer.id = 'sortable-list';
document.body.appendChild(sortableContainer);

// Actualiza la lista ordenable
field.updateSortable();


          
