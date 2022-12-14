<?php
   class collectionClass
   {

      /*************** SETTING THE CONNECTIONS ***************/

      //represents the DB
      private $collectionData;
      
      // set DB insert limit. 
      const MAX_INSERT_ROWS = 1000;
 
      // set the connectiontion 
      public function __construct($configFile)
      {
          if ($ini = parse_ini_file($configFile))
          {
              $collectionPDO = new PDO( "mysql:host=" . $ini['servername'] . 
                                  ";port=" . $ini['port'] . 
                                  ";dbname=" . $ini['dbname'], 
                                  $ini['username'], 
                                  $ini['password']);
              $collectionPDO->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  
              $collectionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
              $this->collectionData = $collectionPDO;
          }
          else
          {
              throw new Exception( "<h2>Creation of database object failed!</h2>", 0, null );
          }
  
      }

      // ref the db
      protected function getDatabaseRef()
      {
         return $this->collectionData;
      }


      /*********************************************************/
      /****************** GET ALL RECORDS **********************/

      public function getAllCollections() 
      {
         $results = [];                  
         $collectionTable = $this->collectionData;

         $stmt = $collectionTable->prepare("SELECT collectionID, collectionName, collectionPub, collectionCond, collectionCost, collectionDate FROM collectionstable ORDER BY collectionID"); 
         
         if ($stmt->execute() && $stmt->rowCount() >0){
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
         }       

         return ($results);
      }
   

      /**********************************************************/
      /*************** MANAGING EXISTING RECORDS ***************/


      public function insertCollection($cName, $cPub, $cCond, $cCost, $cYear)
      {
         $results = [];
         $collectionTable = $this->collectionData;
      
         $stmt = $collectionTable->prepare("INSERT INTO collectionstable SET collectionName = :CName, collectionPub = :CPub, collectionCond = :CCOnd, collectionCost = :CCost, collectionDate = :CYear");
      
         $bindParameters = array
         (
            ":CName" =>$cName,
            ":CPub" => $cPub,
            ":CCOnd"=> $cCond,
            ":CCost"=> $cCost,
            ":CYear"=> $cYear
         );
      
         if ($stmt->execute($bindParameters) && $stmt->rowCount() >0){
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
         }    
         return ($results);
      }
      

      public function getColDetails($id) 
      {
         $results = [];                
         $collectionTable = $this->collectionData;
      
         $stmt = $collectionTable->prepare("SELECT collectionID, collectionName, collectionPub, collectionCond, collectionCost, collectionDate FROM collectionstable WHERE collectionID=:id");
      
         $stmt->bindValue(':id', $id);
               
         if ( $stmt->execute() && $stmt->rowCount() > 0 ) 
         {
            $results = $stmt->fetch(PDO::FETCH_ASSOC);                       
         }
      
         return ($results);
      }
      
      // update a patient's records, where the ID is a match.
      public function updateCollection ($cName, $cPub, $cCond, $cCost, $cYear, $id)
      {
         $isUpdated = false;
         $collectionTable = $this->collectionData;
      
         $stmt = $collectionTable->prepare("UPDATE collectionstable SET collectionName = :CName, collectionPub = :CPub, collectionCond = :CCOnd, collectioNCost = :CCost, collectionDate = :CYear WHERE collectionID=:id");
               
         $bindParameters = array
         (
            ":CName" => $cName,
            ":CPub" => $cPub,
            ":CCOnd"=> $cCond,
            ":CCost"=> $cCost,
            ":CYear"=> $cYear,
            ":id"=>$id
         );
      
         $isUpdated = ($stmt->execute($bindParameters) && $stmt->rowCount() > 0);
      
         return ($isUpdated);
      }

      // delete a patient's record, where the ID is a match.
      public function deleteCollection($id) 
      {
         $results = [];
         $collectionTable = $this->collectionData;
      
         $stmt = $collectionTable->prepare("DELETE FROM collectionstable WHERE collectionID=:id");
               
         $stmt->bindValue(':id', $id);
                  
         if ( $stmt->execute() && $stmt->rowCount() > 0 ) 
         {
            $results = $stmt->fetch(PDO::FETCH_ASSOC);                       
         }
               
         return ($results);
      }


      // Merge the two tables together, via CollectionID primary + foreign key, and have CollectionName display with the Amount Owned.
      public function joinTables() 
      {
         $results = [];                  
         $collectionTable = $this->collectionData;

         $stmt = $collectionTable->prepare("SELECT collectionstable.collectionName, collectioncount.countOwned FROM collectionstable
         LEFT JOIN collectioncount ON collectionstable.collectionID=collectioncount.collectionID
         ORDER BY collectioncount.collectionID"); 
         
         if ($stmt->execute() && $stmt->rowCount() >0){
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
         }       

         return ($results);
      }

      //This should pull JUST the countOwned (amount of a product owned) as paired with its ID from collectionID in collectionsTable.
      public function getCountDetails($id) 
      {
         $results = [];                
         $collectionTable = $this->collectionData;
      
         $stmt = $collectionTable->prepare("SELECT collectionID, countOwned FROM collectionCount WHERE collectionID=:id");
      
         $stmt->bindValue(':id', $id);
         
               
         if ( $stmt->execute() && $stmt->rowCount() > 0 ) 
         {
            $results = $stmt->fetch(PDO::FETCH_ASSOC);                       
         }
      
         return ($results);
      }

      // update a patient's records, where the ID is a match.
      public function updateACount ($cCount, $id)
      {
         $isUpdated = false;
         $collectionTable = $this->collectionData;
      
         $stmt = $collectionTable->prepare("UPDATE collectionCount SET countOwned = :CCount WHERE collectionID=:id");
               
         $bindParameters = array
         (
            ":CCount" => $cCount,
            ":id"=>$id
         );
      
         $isUpdated = ($stmt->execute($bindParameters) && $stmt->rowCount() > 0);
      
         return ($isUpdated);
      }



      /**********************************************************/
      /*************** DESTRUCTOR OF DESTRUCTION ***************/

      // clear memory
      public function __destruct() 
      {
         $this->collectionData = null;
      }



      /* SQL statement for joining */ 
      /* SELECT collectionstable.collectionName, collectioncount.countOwned FROM collectionstable
      LEFT JOIN collectioncount ON collectionstable.collectionID=collectioncount.collectionID
      ORDER BY collectionstable.collectionName;*/

   }
?>