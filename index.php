<!DOCTYPE html>
<html>

<head>
  <title>Lista de Estudiantes</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
</head>

<body class="d-flex flex-column min-vh-100">
  <main class="flex-grow-1">
    <!-- NAV -->
    <section>
      <nav class="navbar navbar-expand-lg bg-dark-subtle">
        <div class="container-fluid">
          <a class="navbar-brand">Escuela</a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
              <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="/">Estudiantes</a>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </section>
    <!-- AGREGAR USUARIO -->
    <section class="my-2 mx-3">
      <button type="button" class="btn btn-outline-success" id="openModalBtn">
        Agregar Estudiante
      </button>
    </section>
    <!-- TABLA -->
    <section class="my-1 mx-3">
      <table class="table table-striped table-hover">
        <thead>
          <tr class="table-primary">
            <th>Carné</th>
            <th>Nombres</th>
            <th>Apellidos</th>
            <th>Dirección</th>
            <th>Teléfono</th>
            <th>Correo Electrónico</th>
            <th>Fecha de Nacimiento</th>
            <th>Tipo de Sangre</th>
            <!-- Otras columnas -->
          </tr>
        </thead>
        <tbody>
          <?php
          include('db_conection.php');
          $conexion = mysqli_connect($hostname, $userName, $userpass, $dbName);

          if (!$conexion) {
            die("La conexión a la base de datos ha fallado: " . mysqli_connect_error());
          }

          $sql = "SELECT * FROM db_empresa.estudiantes e 
            INNER JOIN db_empresa.tipos_sangre ts 
            ON e.id_tipo_sangre = ts.id_tipo_sangre";

          $result = mysqli_query($conexion, $sql);
          mysqli_close($conexion);
          
          // Comprobar si hay resultados
          if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
              // Imprimir las filas de la tabla
              echo '<tr data-estudiante=\'' . json_encode($row) . '\' class="selectable-row">';
              echo '<td>' . $row['carne'] . '</td>';
              echo '<td>' . $row['nombres'] . '</td>';
              echo '<td>' . $row['apellidos'] . '</td>';
              echo '<td>' . $row['direccion'] . '</td>';
              echo '<td>' . $row['telefono'] . '</td>';
              echo '<td>' . $row['correo_electronico'] . '</td>';
              echo '<td>' . date('d M Y', strtotime($row['fecha_nacimiento'])) . '</td>';
              echo '<td>' . $row['sangre'] . '</td>';
              echo '</tr>';
            }
          } else {
            echo '<tr><td colspan="8">No se encontraron estudiantes</td></tr>';
          }

          ?>

        </tbody>
      </table>
    </section>
    <!-- Modal -->
    <section>
      <div id="myModal" class="modal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="tituloAdd">Agregar estudiante</h5>
              <button type="button" id="cerrarDialogButton" class="btn-close" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div id="modalContent"></div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const openModalBtn = document.getElementById('openModalBtn');
      const modal = document.getElementById('myModal');
      const cerrarDialogButton = document.getElementById('cerrarDialogButton')
      const modalContent = document.getElementById('modalContent');
      const selectableRows = document.querySelectorAll('.selectable-row');

      // Evento para abrir el modal con el formulario
      openModalBtn.addEventListener('click', async () => {
        try {
          const response = await fetch(`http://localhost/add.php`);
          const addFormContent = await response.text();
          modalContent.innerHTML = addFormContent;
          const titulo = document.getElementById('tituloAdd');
          titulo.textContent = 'Agregar Estudiante';
          modal.style.display = 'block';
        } catch (error) {
          console.error(error);
        }
      });

      // Evento para cerrar el modal si se hace clic fuera de su contenido
      modal.addEventListener('click', (event) => {
        if (event.target === modal) {
          modal.style.display = 'none';
        }
      });

      // Evento para cerrar el modal si se hace clic fuera de su contenido
      cerrarDialogButton.addEventListener('click', (event) => {
        if (event.target === cerrarDialogButton) {
          modal.style.display = 'none';
        }
      });

      // Evento para mostrar los detalles del estudiante en el modal al hacer clic en una fila
      selectableRows.forEach(row => {
        row.addEventListener('click', async () => {
          const response = await fetch(`http://localhost/add.php`);
          const addFormContent = await response.text();
          modalContent.innerHTML = addFormContent;
          const estudianteData = row.getAttribute('data-estudiante');
          const estudiante = JSON.parse(estudianteData);

          // Obtén los campos del formulario
          const idEstudianteInput = document.querySelector('#myModal [name="id_estudiante"]');
          const carneInput = document.querySelector('#myModal [name="carne"]');
          const nombresInput = document.querySelector('#myModal [name="nombres"]');
          const apellidosInput = document.querySelector('#myModal [name="apellidos"]');
          const direccionInput = document.querySelector('#myModal [name="direccion"]');
          const telefonoInput = document.querySelector('#myModal [name="telefono"]');
          const correoInput = document.querySelector('#myModal [name="correo_electronico"]');
          const tipoSangreInput = document.querySelector('#myModal [name="id_tipo_sangre"]');
          const fechaNacimientoInput = document.querySelector('#myModal [name="fecha_nacimiento"]');

          // Llena los valores en los campos del formulario
          idEstudianteInput.value = estudiante.id_estudiante;
          carneInput.value = estudiante.carne;
          nombresInput.value = estudiante.nombres;
          apellidosInput.value = estudiante.apellidos;
          direccionInput.value = estudiante.direccion;
          telefonoInput.value = estudiante.telefono;
          correoInput.value = estudiante.correo_electronico;
          const tipoSangreOptions = tipoSangreInput.options;
          for (let i = 0; i < tipoSangreOptions.length; i++) {
            if (tipoSangreOptions[i].value === estudiante.id_tipo_sangre.toString()) {
              tipoSangreOptions[i].selected = true;
              break;
            }
          }

          const fechaNacimiento = new Date(estudiante.fecha_nacimiento);
          fechaNacimientoInput.value = fechaNacimiento.toISOString().split('T')[0];
          modal.style.display = 'block';

          const titulo = document.getElementById('tituloAdd');
          titulo.textContent = 'Editar Estudiante';

          const spaceForButton = document.getElementById('space-for-button');
          //BOTON DE ACTUALIZAR
          const updateButton = document.createElement('button');
          updateButton.type = 'submit';
          updateButton.textContent = 'Actualizar';
          updateButton.setAttribute('name', 'update');
          updateButton.setAttribute('value', 'true');
          updateButton.setAttribute('class', 'btn btn-info ')
          //BOTON DE ELIMINAR
          const deleteButton = document.createElement('button');
          deleteButton.type = 'submit'; 
          deleteButton.textContent = 'Eliminar';
          deleteButton.setAttribute('name', 'delete');
          deleteButton.setAttribute('value', 'true');
          deleteButton.setAttribute('class', 'btn btn-danger');
          deleteButton.addEventListener('click', () => {
            const confirmDelete = window.confirm('¿Estás seguro de que deseas eliminar al estudiante?');
            if (confirmDelete) {
              const form = document.querySelector('#myModal form');
              form.submit();
              // modal.style.display = 'none';
            } else {
              event.preventDefault();
            }
          });
          spaceForButton.innerHTML = '';
          spaceForButton.appendChild(updateButton);
          spaceForButton.appendChild(deleteButton);
          spaceForButton.setAttribute('class', 'd-grid gap-2 col-12 mx-auto')
        });
      });
      //
    });
  </script>
</body>

</html>