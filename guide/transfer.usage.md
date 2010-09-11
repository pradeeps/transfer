# Usando Transfer

Usar el módulo Transfer es un proceso realmente sencillo e intuitivo. Se encuentran disponibles 7 métodos
para realizar transferencias de archivos y otras operaciones con archivos y directorios.

## Instanciar la clase Transfer

Para trabajar con la clase necesitamos obtener una instancia. Este proceso crea una sola instancia por grupo 
de configuración, haciendo uso del patrón _singleton_, lo que nos ayuda a ahorrar memoria y mantener la conexión
a través de las diferentes partes de nuestra aplicación.

	// Usando el grupo de configuración por defecto
	$my_transfer = Transfer::instance();
	
	// Usando el grupo 'sftp'
	$my_ftp = Transfer::instance('sftp');

## Descargar un archivo

Para la transferencia de un archivo remoto a nuestro sistema local (descarga) disponemos del método __download__ cuya 
declaración es __download($remote_file, $local_file)__, donde `remote_file` es la ruta del archivo remoto y `local_file` 
la ruta del archivo local donde será almacenado tras la descarga.

_Ejemplo de descarga de un archivo_

	if( Transfer::instance()->download('/pub/archivo.bin', '/home/usuario/archivo-descargado.bin') )
	{
		echo 'Descarga efectuada correctamente';
	}
	else
	{
		echo 'Falló la descarga';
	}

## Subir un archivo

El método __upload__ nos permite subir un archivo local al servidor remoto: __upload($local_file, $remote_file, $create_mode)__, donde
`local_file` es la ruta del archivo local a subir, `remote_file` la ruta del servidor donde deberá almacenarse y `create_mode` el modo
o permisos de unix con los que se creará el archivo en el caso de que nuestro servidor soporte esta opción.

_Ejemplo de subida de un archivo_

	if( Transfer::instance()->upload('/home/usuario/discurso.odt', '/congreso/caade2010/discurso.odt') )
	{
		echo 'Archivo almacenado correctamente';
	}
	else
	{
		echo 'Falló la subida';
	}

## Renombrar un archivo o directorio

El método __rename($from, $to)__ permite cambiar de nombre de un archivo o directorio, siendo `from` la ruta al archivo original y `to`
la ruta al archivo con el nombre ya cambiado.

_Ejemplo del cambio de nombre en un archivo_

	Transfer::instance()->rename('/pub/binarytree.x', '/pub/arbolbinario.x');

## Eliminar un archivo

El método __delete($remote_file)__ elimina el archivo situado en la ruta del servidor remoto especificada en `remote_file`.

_Ejemplo de la eliminación de un archivo_

	Transfer::instance()->delete('/docs/caade2010/guide.pdf');

## Crear un directorio

__mkdir($dirname, $mode, $recursive)__ Crea el directorio especificado en `dirname` con los permisos `mode` (si el servidor lo permite). Si los directorios 
situados en la ruta no existen el directorio no será creado a no ser que `recursive` sea true, en cuyo caso también serán creados estos directorios intermedios.

_Ejemplo de creación de un directorio_

	Transfer::instance()->mkdir('/pub/files/xyz', 0755, true);

## Eliminar un directorio

Para eliminar un directorio este debe estar vacío (sin archivos). El método __rmdir($dirname)__ elimina el directorio situado en `dirname`.

_Ejemplo de la eliminación de un directorio vacío_

	Transfer::instance()->rmdir('/pub/files/xyz');

## Ejecutar un comando en el servidor

El método __exec($command)__ envía el comando especificado en `command` al servidor y devuelve su respuesta. Dependiendo del driver y servidor usado,
puede devolver una cadena (_string_) o un vector (_array_) con la/las respuestas.

_Ejemplo de ejecución de un comando_

	$response = Transfer::instance()->exec('ls -la');
	
	var_dump($response);
