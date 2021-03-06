<?php 
/**
* Clase para obtener información de productos
*
* Clase que permite obtener 
* información relacionada con productos
*
* @package admingim
* @author Dario <darioh.dev@gmail.com>
* @version 0.4
*/
class Productos extends Model{
	/**
	* Función que devuelve todas las productos.
	*
	* Si hay dudas sobre la herencia, chequear {@link Model la clase madre}.
	*
	* @return array retorna un array con la información de la query.
	*/
	public function getTodos(){
		$this->db->query("select * from productos");
		return $this->db->fetchAll();
	}
	
	/**
	* Función que permite buscar un producto
	*
	* Si hay dudas sobre la herencia, chequear {@link Model la clase madre}.
	*
	* @param string $nombre string que representa el nombre a buscar
	* @return array retorna el array de la query
	*/
	public function busquedaProducto($nombre){
		if(strlen($nombre)<1) die("error1");
		$nombre=substr($nombre,0,40);
		$nombre=$this->db->escapeString($nombre);
		$nombre = str_replace("%", "\%", $nombre);
		$nombre = str_replace("_", "\_", $nombre);
		$this->db->query("select * from productos
							where nombre LIKE '%$nombre%'");
		return $this->db->fetchAll();
	}

	/**
	* Función que permite dar de alta un producto
	*
	* Si hay dudas sobre la herencia, chequear {@link Model la clase madre}.
	*
	* @param string $nombre string que representa al nuevo nombre
	* @param int $categoria otro int que representa la categoria a cambiar
	* @param real $precio real que representa el nuevo precio
	* @param int $stock otro int que representa el stock del producto
	* @param int $pto_reposicion otro int que representa el nuevo punto de reposición
	*/
	public function altaProducto($nombre,$categoria,$precio,$stock,$pto_reposicion){
		if(strlen($nombre)<2) die("error1");
		$nombre=substr($nombre,0,30);
		$nombre=$this->db->escapeString($nombre);

		$c=new Categorias;
		if(!$c->existeCategoria($categoria)) die("error2"); //delego la validacion de cargos a cargos.php

		$this->db->query("insert into productos	
							(nombre,id_categoria,precio_venta,pto_reposicion,stock)
							values
							('$nombre',$categoria,$precio,$stock,$pto_reposicion)");

	}

	/**
	* Función que permite buscar un producto en específico
	*
	* Si hay dudas sobre la herencia, chequear {@link Model la clase madre}.
	*
	* @param int $id un int que corresponde al codigo de producto a buscar
	* @return array retorna el array de la query
	*/
	public function productoEspecifico($id){
		if(!ctype_digit($id)) die("error id");
		$this->db->query("select p.precio_venta,p.stock,p.pto_reposicion,codigo_producto,c.nombre nombrecat,p.nombre,c.id_categoria,p.id_categoria
							from productos p
							left join categoria_producto c on c.id_categoria= p.id_categoria
							where codigo_producto=$id");
		if($this->db->numRows()!=1) die("error numrows");
		return $this->db->fetch();
		
	}

	/**
	* Función que permite modificar producto
	*
	* Si hay dudas sobre la herencia, chequear {@link Model la clase madre}.
	*
	* @param string $nombre string que representa al nuevo nombre
	* @param int $id otro int que representa al codigo de producto a modificar
	* @param int $categoria otro int que representa la categoria a cambiar
	* @param real $precio real que representa el nuevo precio
	* @param int $stock otro int que representa el stock del producto
	* @param int $pto_reposicion otro int que representa el nuevo punto de reposición
	*/
	public function modificacionProducto($nombre,$id,$categoria,$precio,$stock,$pto_reposicion){
		if(!ctype_digit($id)) die("error id");
		if(!is_numeric($precio)) die("error id");
		if(!ctype_digit($stock)) die("error id");
		if(!ctype_digit($pto_reposicion)) die("error id");
		if(strlen($nombre)<2) die("error1");
		$nombre=substr($nombre,0,30);
		$nombre=$this->db->escapeString($nombre);

		$c=new Categorias;
		if(!$c->existeCategoria($categoria)) die("error2"); //delego la validacion de cargos a cargos.php




		$this->db->query("UPDATE productos	
							SET nombre='$nombre',id_categoria=$categoria,precio_venta=$precio,stock=$stock,pto_reposicion=$pto_reposicion
                            WHERE codigo_producto=$id");

	}

    /**
	* Función que muestra los productos de un proveedor
	*
	* Si hay dudas sobre la herencia, chequear {@link Model la clase madre}.
	*
	* @param string $cuit representa al cuit de proveedor
	* @return array retorna el array de la query
	*/
	public function productoDeProveedor($cuit){
		if(strlen($cuit)<2) die("error1");
		$cuit=substr($cuit,0,15);
		$cuit=$this->db->escapeString($cuit);
							
		$this->db->query("select * from productos p
							left join productos_prov pp on p.codigo_producto= pp.codigo_producto
							where pp.cuit=$cuit");
		return $this->db->fetchAll();
	}

	/**
	* Función que muestra los proveedores de un producto
	*
	* Si hay dudas sobre la herencia, chequear {@link Model la clase madre}.
	*
	* @param int $cod int que representa el codigo de producto
	* @return array retorna el array de la query
	*/
	public function proveedoresDeProducto($cod){
		if(!ctype_digit($cod)) die("error codigo"); 
		$this->db->query("select * from proveedores p
							left join productos_prov pp on p.cuit= pp.cuit
							where pp.codigo_producto=$cod");
		return $this->db->fetchAll();
	}


	/**
	* Función que da de alta un producto que provee un proveedor
	*
	* Si hay dudas sobre la herencia, chequear {@link Model la clase madre}.
	*
	* @param string $cuit representa al cuit de proveedor
	* @param int $codigo_producto int que representa el codigo de producto
	* @param real $precio que representa al nuevo producto proveido
	*/
	public function altaProvisto($cuit,$codigo_producto,$precio){
		if(strlen($cuit)<2) die("error1");
		$cuit=substr($cuit,0,15);
		$cuit=$this->db->escapeString($cuit);


		if(!ctype_digit($codigo_producto)) die("error id");
		
		$this->db->query("insert into productos_prov	
							(cuit,codigo_producto,precio_producto)
							values
							($cuit,$codigo_producto,$precio)");
    }
	

	/**
	* Función que borra un producto que provee un proveedor
	*
	* Si hay dudas sobre la herencia, chequear {@link Model la clase madre}.
	*
	* @param string $cuit representa al cuit de proveedor
	* @param int $codigo_producto int que representa el codigo de producto
	*/
	public function bajaProvisto($cuit,$codigo_producto){
		if(!ctype_digit($codigo_producto)) die("error id");
		
		$this->db->query("DELETE FROM productos_prov	
							WHERE cuit=$cuit and codigo_producto=$codigo_producto
							LIMIT 1
							");
	}

	/**
	* Función que muestra los productos que provee un proveedor
	*
	* Si hay dudas sobre la herencia, chequear {@link Model la clase madre}.
	*
	* @param string $cuit representa al cuit de proveedor
	* @param int $codigo int que representa el codigo de producto
	* @return array retorna el array de la query
	*/
	public function provistoEspecifico($cuit,$codigo){
		if(!ctype_digit($codigo)) die("error id");
		if(strlen($cuit)<2) die("error1");
		$cuit=substr($cuit,0,15);
		$cuit=$this->db->escapeString($cuit);


        $this->db->query("select * from productos_prov pp
                            left join productos p, proveedores pr
							where p.codigo_producto=pp.codigo_producto AND pr.cuit=pp.cuit AND $cuit=pp.cuit AND $codigo=pp.codigo_producto");
		if($this->db->numRows()!=1) die("error numrows");
		return $this->db->fetch();
    }

	/**
	* Función que muestra precio de producto según proveedor
	*
	* Si hay dudas sobre la herencia, chequear {@link Model la clase madre}.
	*
	* @param int $cod int que representa el codigo de producto
	* @param string $cuit representa al cuit de proveedor
	* @return array retorna el array de la query
	*/
	public function precio($cod,$cuit){
		if(!ctype_digit($cod)) die("error id");
		


		$this->db->query("select * from productos_prov pp
							left join productos p on pp.codigo_producto=p.codigo_producto
							where pp.cuit=$cuit AND pp.codigo_producto=$cod");
							
							return $this->db->fetchAll();
	}


	
}


?>