<?php
	require_once("table.php");
	class Mysql
	{
		private $c;
		private $d;
		private $query_count=0;
		private $query_array = array();
		public $print_debug=true;
		
		function __construct($database, $ip, $un, $pw, $port=3306)
		{
			$this->c = mysqli_connect($ip,$un,$pw,"",$port) or die(mysqli_connect_error());
			$this->d = mysqli_select_db($this->c,$database) or die(mysqli_error($this->c));
		}
		
		private function error($q,$e)
		{
			echo "Query - ".$q."<br>";
			echo "Error - ".$e."<br>";
			$data = debug_backtrace();
			$ta = new Table();
			$ta -> tablize($data);
			echo "<br>";
			
		}
		
		public function query($q)
		{
			if($this->print_debug)
				echo "\n<!--$q-->\n";
			$r = mysqli_query($this->c,$q) or trigger_error($q,$this->error($q,mysqli_error($this->c)));
			$this -> query_count++;
			$this->query_array[] = $q;
			return $r;
		}
		
		public function num_entries($r)
		{
			$count = 0;
			if(is_string($r))
				$r = $this->query($r);
			if($r instanceof mysqli_result)
			{
				$count = mysqli_num_rows($r);
			}
			return $count;
		}
		
		public function query_id($q)
		{
			$this -> query($q);
			return mysqli_insert_id($this -> c);
		}
		
		public function count_entries($r)
		{
			return $this -> num_entries($r);
		}
		
		public function print_num_queries()
		{
			echo "<!--MYSQL QUERIES: " . $this-> query_count."--!>\n";
			foreach($this -> query_array as $query)
			{
				echo "<!--MYSQL Q: " . $query . " -->\n";
			}
		}
		
		public function fetch_array($q,$t=MYSQLI_BOTH,$compress=true)
		{
			switch($t)
			{
				case MYSQL_ASSOC:	$t=MYSQLI_ASSOC; break;
				case MYSQL_NUM:		$t=MYSQLI_NUM; break;
				case MYSQL_BOTH:	$t=MYSQLI_BOTH; break;
			}
			$retArr = array(); 
			if(is_string($q))
				$q = $this->query($q);
			if($q instanceof mysqli_result)
			{
				$s = $this->num_entries($q);
				if($s > 1 || ($s == 1 && $compress == false))
				{
					while($r=mysqli_fetch_array($q,$t))
					{
						$retArr[] = $r;
					}
				}
				elseif($s == 1)
				{
					$r=mysqli_fetch_array($q,$t);
					$retArr = $r;
				}
			}
			return $retArr;
		}
		
		public function fetch_field($q,$f)
		{
			$data = $this -> fetch_array($q,MYSQLI_BOTH,$compress=false);
			$retArr = array();
			if(is_array($data) && count($data) > 0)
			{
				foreach($data as $key => $row)
				{
					if(array_key_exists($f,$row))
					{
						$retArr[$key] = $row[$f];
					}
				}
			}
			return $retArr;
		}
		
		/*
		 * insert_update will insert values into table or update if the keys already exist
		 * parameters are (table, values, keys, doUpdate, extra)
		 * table=The table to insert into
		 * values=an array of the form(key=>value) where key correpsonds to the table field and value is the value to insert into the table
		 * keys=an array with the names of the keys to use in the where statement (values from the values array)
		 * doUpdate=boolean, true if insert/update, false if insert only
		 * extra = any extra which conditionals to use on the where statements
		 */
		public function insert_update($table, $values,$keys,$du=true,$extra="",$di=true)
		{
			/*
			 * CHECK IF RECORD EXISTS ALREADY
			 */
			
			$q = "SELECT * FROM " .$table ." WHERE ";
			$w = "";
			foreach($keys as $key)
			{
				if(array_key_exists($key,$values))
					$w .= " " . $key . "='".$values[$key]."' AND";
			}
			$w .= $extra;
			if(substr($w,-3) == "AND")
				$w = substr($w,0,-3);
			$q = $q . $w;
			
			if($this -> num_entries($q) >= 1)
			{
				/*
				 * UPDATE EXISTING VALUES IN TABLE!
				 */
				if($du)
				{
					$q = "UPDATE " . $table . " SET ";
					foreach($values as $key => $value)
						$q .= $key . "='".mysql_real_escape_string($value)."', ";
					if(substr($q,-2) == ", ")
						$q = substr($q,0,-2) . " ";
					$q = $q . " WHERE " . $w;
					return $this -> query_id($q);
				}
				else
					return false;
			}
			else
			{
				/*
				 * INSERT NEW VALUES INTO TABLE!
				 */
				if($di)
				{
					$q = "INSERT INTO " . $table . " (";
					foreach($values as $key => $value)
						$q .= $key . ",";
					if(substr($q,-1) == ",")
						$q = substr($q,0,-1);
					$q .= ") VALUES (";
					foreach($values as $key => $value)
						$q .= "'" . mysql_real_escape_string($value)."',";
					if(substr($q,-1) == ",")
						$q = substr($q,0,-1);
					$q .= ")";
					return $this -> query_id($q);
				}
				return false;
			}
			
		}
		
		public function print_query($q)
		{
			if(!is_array($q))
				$q = $this->fetch_array($q,MYSQLI_ASSOC);
			$ta = new Table();
			$ta->tablize($q);
			unset($ta);
		}
	}
?>
