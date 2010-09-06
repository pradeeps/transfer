# Configuración para Transfer

El módulo Transfer hace uso de los archivos de configuración para preestablecer conexiones a diferentes
servidores usando un protocolo (driver) específico. Cada grupo de configuración permite definir el driver 
que debe ser usado así como los parámetros de acceso al servicio.

Para crear una conexión usando un grupo específico sólo tenemos que hacer `Transfer::factory('grupo')`. En 
el caso de no necesitar nada más que un grupo, este puede ser llamado `default` y entonces no resulta necesario
pasarle el nombre del grupo al instanciar un nuevo elemento de Transfer.

## El archivo de configuración

El módulo no trae consigo ningún grupo definido, por lo que el primer paso es crear nuestro archivo de configuración.
El lugar correcto para hacerlo es en `application/config/transfer.php`. En su interior, el código sigue la misma estructura
que los demás archivos de configuración en Kohana:

	'default' => array(
			'driver'      => 'FTP',
			'hostname'    => 'localhost',
			'port'        => 21,
			'username'    => '',
			'password'    => '',
	),

Existe una serie de opciones que son comunes en los distintos drivers que se incluyen:

Nombre       | Requerido | Descripción
------------ | --------- | ------------------------------------------------
driver       | __SI__    | (_string_) El driver a usar en esta conexión
hostname     | __SI__    | (_string_) Dirección IP o dominio
port         | __SI__    | (_int_) Puerto

De forma adicional, cada driver podrá necesitar de opciones de configuración específicas para
su funcionamiento. Estas se describen a continuación.

### Driver FTP

[Información oficial de PHP](http://php.net/manual/en/book.ftp.php)

__Opciones de autentificación__

Nombre       | Requerido | Descripción
------------ | --------- | ------------------------------------------------
username     | __NO__    | (_string_) Nombre de usuario
password     | __NO__    | (_string_) Contraseña

Si no se especifica alguna de estas dos opciones, se realizará una conexión anónima.


### Driver SFTP (Secure FTP)

[Información oficial de PHP](http://php.net/manual/en/book.ssh2.php)

Este driver dispone de dos formas de identificarse como usuario: por certificados de clave pública/privada
o mediante el uso del tradicional par de usuario/contraseña. Si se especifica las opciones para la conexión
por certificado, se utilizará este método, si no, el tradicional será usado.

__Opciones de autentificación por certificado__

Nombre       | Requerido | Descripción
------------ | --------- | ------------------------------------------------
username     | __SI__    | (_string_) Nombre de usuario con el que hacer login
pubkeyfile   | __SI__    | (_string_) Ruta al archivo con la clave pública del certificado
privkeyfile  | __SI__    | (_string_) Ruta al archivo con la clave privada del certificado
password     | __NO__    | (_string_) Si la tiene, contraseña del certificado

_Ejemplo de configuración usando certificados_

	'sftp'    => array(
		'driver'      => 'SFTP',
		'hostname'    => 'localhost',
		'port'        => 22,
		'username'    => 'user',
		'pubkeyfile'  => '/path/to/id_rsa.pub',
        'privkeyfile' => '/path/to/id_rsa',
		'password'    => 'mycertpasswd',
	),

__Opciones de autentificación tradicional__

Nombre       | Requerido | Descripción
------------ | --------- | ------------------------------------------------
username     | __SI__    | (_string_) Nombre de usuario
password     | __SI__    | (_string_) Contraseña

__Validación de la firma del servidor__

Cuando se trabaja en un entorno seguro, debemos verificar que realmente estamos conectados
a la máquina con la que queremos trabajar, y que no lo estamos haciendo a otra máquina diferente
que ha suplantado la identidad de la nuestra. Para ello, el servidor SSH envía una "huella" única
ese servidor, que nos puede servir para identificarlo y comprobar que estamos conectados a donde
queremos estarlo. Este driver incorpora la posibilidad de realizar una comparación de esta huella
al realizar la conexión, de forma que si no coincide no se continuará con el proceso de autentificación
y una excepción será lanzada. 

Nombre       | Requerido | Descripción
------------ | --------- | ------------------------------------------------
fingerprint  | __NO__    | (_string_) Huella para realizar la comparación

_Ejemplo de configuración haciendo uso de la comprobación de la huella_

	'sftp'    => array(
		'driver'      => 'SFTP',
		'hostname'    => 'localhost',
		'port'        => 22,
		'username'    => 'user',
		'password'    => 'mypasswd',
		'fingerprint' => 'ef095fdb6b4fa98cf7f5f4ea5dfdd42e',
	),


