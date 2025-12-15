<?php
/**
 * 
 * Clase para conectar a BD y adicional ORM
 */


class Base
{
    //Inicialización de parametris
    private $dbh; //Handler de BD
    private $stmt; //Manejo de Sentencia 
    private $table; //Tabla a utilizar
    private $wheres = [];
    private $orderBy = [];
    private $bindings = [];
    private $limit;
    private $offset;

    private $user = DBUSER;
    private $pwd = DBPWD;
    private $driver = DBDRIVER;
    private $host = DBHOST;
    private $port = DBPORT;
    private $db = DBNAME;
    private $charset = 'utf8mb4';


    public function __construct($table)
    {
        $this->table = $table;
        $options = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,  //Que regrese un arreglo asociativo
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //Que maneje errores con excepciones
            PDO::ATTR_EMULATE_PREPARES => false //Que no se puedan hacer inyecciones SQL
        ];
        try {
            $dsn = "{$this->driver}:host={$this->host};port={$this->port};dbname={$this->db};charset={$this->charset}";
            $this->dbh = new PDO($dsn, $this->user, $this->pwd, $options);
            //echo 'Conexion Exitosa a Base de Datos';
        } catch (PDOException $e) {
            echo 'Error en conexion a Base de Datos ' . $e->getMessage();
        }
    } //Fin de construct

    /**
     * No es recomendable tener el siguiente codigo aqui
     */

    /**
     * @sql string Cadena de consulta
     */


    public function query($sql)
    {
        $this->stmt = $this->dbh->prepare($sql);

        //Temporal

        // $this->stmt->bindValue(':id',2,PDO::PARAM_INT);
        // $this->stmt->execute();
        // return $this->stmt->fetch(PDO::FETCH_BOTH);

        //Fin temporal
    }

    public function bind($parametro, $valor, $tipo = null)
    {
        switch (is_null($tipo)) {
            case is_int($valor):
                $tipo = PDO::PARAM_INT;
                break;
            case is_bool($valor):
                $tipo = PDO::PARAM_BOOL;
                break;
            case is_null($valor):
                $tipo = PDO::PARAM_NULL;
                break;
            default:
                $tipo = PDO::PARAM_STR;
                break;
        }
        $this->stmt->bindValue($parametro, $valor, $tipo);

    }

    public function execute()
    {
        return $this->stmt->execute();
    }


    public function get()
    {
        $sql = "SELECT * FROM {$this->table}";
        $sql .= $this->addWheres();
        // $sql.= $this->addOrderBys();
        if ($this->limit) {
            $sql .= " LIMIT " . $this->limit;
        }
        $this->query($sql);
        //Vincular con bind
        foreach ($this->bindings as $param => $value) {
            $this->bind($param, $value);
        }
        $this->execute();
        $resultado = $this->stmt->fetchAll();
        $this->limpiar();

        return $resultado;
    }

    public function where($column, $value, $operator = '=')
    {
        $this->wheres[] = ['type' => 'AND', 'column' => $column, 'value' => $value, 'operator' => $operator];
        return $this;

    }

    public function addWheres()
    {
        if (empty($this->wheres)) {
            return '';
        }
        $this->bindings = [];
        $clauses = [];
        foreach ($this->wheres as $index => $where) {
            $param=":where_{$index}";
            switch ($where['operator']) {
                case 'IN':
                case 'NOT IN':
                    //To do
                    break;
                case 'IS NULL':
                case 'IS NOT NULL':
                    //To do
                    break;
                default:
                    $placeholders='Otro metodo';
                    $clause="{$where['column']} {$where['operator']} {$param}";
                    $this->bindings[$param] = $where['value'];

                    //CRea la cadena


            }
            if($index==0){
                $clauses[]=$clause;
            }else{
                $clauses[]=" {$where['type']} {$clause} ";
            }
        }
        
        return " WHERE ". implode(' ', $clauses);
    }

    public function find($id)
    {
        return $this->where('id', $id)->first();
    }

    public function first()
    {
        $this->limit(1);
        $result = $this->get();
        
        return $result[0] ?? null;
    }

    public function limit($limit)
    {
        //Agregar a la consulta el limite
        $this->limit = $limit;
        return $this; //Uso de fluent
    }

    /**
     * metodo update()
     */

    public function update($data){
        //"UPDATE tabla SET campos WHERE campo = :campo";
        $sets=[];
        foreach($data as $key => $value){
            $sets[]="$key = :set_$key";
        }
        $sets = implode(', ',$sets);

        $sql = "UPDATE {$this->table} SET $sets " . $this->addWheres();
        // dd($sql);
        $this->query($sql);
        // aplicar binds
        foreach($data as $key => $value){
            $this->bind(":set_$key",$value);
        }
        // aplicar binds de las condiciones WHERE
        foreach ($this->bindings as $param => $value) {
            $this->bind($param, $value);
        }

        $this->execute();
        // conteo de renglones para renglones afectados
        $afectados = $this->afectados();
        // dd($this->stmt->rowCount());
        $this->limpiar();
        return $afectados; //mañana

    } // fin de update

    /**
     * metodo create()
     */

    public function create($data){
        
        
        $columns = implode(', ',array_keys($data));
        $placeholders = ':' . implode(', :',array_keys($data));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        // dd($sql);
        $this->query($sql);

        // para campos
        foreach($data as $key => $value){
            $this->bind(":{$key}",$value);
        }

        $this->execute();
        // conteo de renglones para renglones afectados
        $lastId = $this->dbh->lastInsertId();
        // dd($this->stmt->rowCount());
        $this->limpiar();
        return $lastId; 

    } // fin de create

    /**
     * metodo de afectados
     */

    public function afectados(){
        return $this->stmt ? $this->stmt->rowCount(): 0;
    }

    /**
     * metodo de limpiar
     */

    public function limpiar(){
        $this -> wheres = [];
        $this -> orderBy = [];
        $this -> bindings = [];
        $this -> limit = null;
        $this -> offset = null;
        $this -> stmt = null;
    }

    /**
     * metodo delete()
     */

    public function delete(){
        $sql = "DELETE FROM {$this->table} " . $this->addWheres();
        $this->query($sql);
        
        // aplicar binds de las condiciones WHERE
        foreach ($this->bindings as $param => $value) {
            $this->bind($param, $value);
        }
        $this->execute();

        $afectados = $this->afectados();
        // dd($this->stmt->rowCount());
        $this->limpiar();
        return $afectados;
    }

    /**
     * Iniciar transacción
     */
    public function beginTransaction() {
        return $this->dbh->beginTransaction();
    }

    /**
     * Confirmar transacción
     */
    public function commit() {
        return $this->dbh->commit();
    }

    /**
     * Revertir transacción
     */
    public function rollBack() {
        return $this->dbh->rollBack();
    }

    /**
     * Obtener múltiples resultados
     */
    public function resultSet() {
        $this->execute();
        return $this->stmt->fetchAll();
    }

    /**
     * Obtener un solo resultado
     */
    public function single() {
        $this->execute();
        return $this->stmt->fetch();
    }

    /**
     * Obtener el último ID insertado
     */
    public function lastInsertId() {
        return $this->dbh->lastInsertId();
    }

} //Fin de la clase
