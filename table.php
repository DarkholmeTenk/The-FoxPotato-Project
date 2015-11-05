<?php
	class Table
	{
		//Style contents
		//class,id,inline,extra
		private $taStyle = array();
		private $thStyle = array();
		private $tdStyle = array();
		private $trStyle = array();
		
		public function __construct($styles=array())
		{
			if(array_key_exists("table",$styles))
				$this->taStyle = $styles["table"];
			if(array_key_exists("th",$styles))
				$this->thStyle = $styles["th"];
			if(array_key_exists("td",$styles))
				$this->tdStyle = $styles["td"];
			if(array_key_exists("tr",$styles))
				$this->trStyle = $styles["tr"];
		}
		
		private function item($item,$style,$data)
		{
			$rs = "<" . $item;
			
			$cs = -1;
			if(is_integer($data))
				$cs = $data;
			elseif(array_key_exists("cs",$data))
				$cs = $data["cs"];
			if($cs != -1)
				$rs = $rs . " colspan='".$cs."'";
			
			$class="";
			if(array_key_exists("class",$style))
				$class=$style["class"];
			if(array_key_exists("class",$data))
				$class=$data["class"];
			if($class != "")
				$rs = $rs . " class='".$class."'";
			
			$id="";
			if(array_key_exists("id",$style))
				$id=$style["id"];
			if(array_key_exists("id",$data))
				$id=$data["id"];
			if($id != "")
				$rs = $rs . " id='".$id."'";
			
			$inline="";
			if(array_key_exists("inline",$style))
				$inline.=$style["inline"];
			if(array_key_exists("inline",$data))
				$inline.=$data["inline"];
			if($inline != "")
				$rs = $rs . " style='".$inline."'";
			$extra="";
			if(array_key_exists("extra",$style))
				$extra.=$style["extra"];
			if(array_key_exists("extra",$data))
				$extra.=$data["extra"];
			if($extra != "")
				$rs = $rs . " ".$extra." ";
			
			$rs = $rs . ">";
			return $rs;
		}
		
		public function td($data=array(),$close=false)
		{
			if($close)
				return "</td>";
			else
				return $this->item("td",$this->tdStyle,$data);
		}
		
		public function th($data=array(),$close=false)
		{
			if($close)
				return "</th>";
			else
				return $this->item("th",$this->thStyle,$data);
		}
		
		public function tr($data=array(),$close=false)
		{
			if($close)
				return "</tr>";
			else
				return $this->item("tr",$this->trStyle,$data);
		}
		
		public function table($data=array(),$close=false)
		{
			if($close)
				return "</table>";
			else
				return $this->item("table",$this->taStyle,$data);
		}
		
		public function ta($data=array(),$close=false)
		{
			return $this->table($data,$close);
		}
		
		public function tablize($arr = array())
		{
			if(is_array($arr))
			{
				if(count($arr) > 0)
				{
					$headArr = array();
					foreach($arr as $key=>$v)
					{
						if(is_array($v))
						{
							foreach($v as $k=>$w)
							{
								if(in_array($k,$headArr,true) == false)
									$headArr[]=$k;
							}
						}
						else
						{
							if(!in_array($key,$headArr))
								$headArr[]=$key;
						}
					}
					echo $this->ta();
					echo $this->tr();
					foreach($headArr as $v)
						echo $this->th() . $v;
					
					if(array_key_exists($headArr[0],$arr))
						if(is_array($arr[$headArr[0]]) == false)
							echo $this->tr();
					
					foreach($arr as $k=>$v)
					{
						if(is_array($v))
						{
							echo $this->tr();
							foreach($headArr as $h)
							{
								echo $this->td();
								if(array_key_exists($h,$v))
								{
									$x = print_r($v[$h],true);
									echo $x;
								}
								echo $this->td(null,true);
							}
							echo $this -> tr(null,true);
						}
						else
						{
							echo $this->td();
							$x = print_r($v,true);
							echo $x;
							echo $this->td(null,true);
						}
					}
					echo $this -> tr(null,true);
					echo $this -> ta(null,true);
				}
				else
				{
					Throw new Exception('Empty array');
				}
			}
			else
			{
				Throw new Exception('Not an array');
			}
		}
	}
?>