<?php
require "database.php";
function connexionE($db,$email,$password):bool{
    $requete=$db->prepare('SELECT * from utilisateur where email=? and mot_de_passe=?');
    $requete->execute(array($email,$password));
    if($requete->rowCount()>0){
        return true;
    }else{
        return false;
    }
}
function connexionA($db,$email,$password):bool{
    $requete=$db->prepare('SELECT * from administrateur where email=? and passworde=?');
    $requete->execute(array($email,$password));
    if($requete->rowCount()>0){
        return true;
    }else{
        return false;
    }
}
function recupUtilByMail($db,$email){
    $requete=$db->prepare('SELECT * from utilisateur where email=?');
    $requete->execute(array($email));
    while($ligne=$requete->fetch(PDO::FETCH_ASSOC)){
        $etudiant= ['id'=>$ligne['id_etudiant'],'nom'=>$ligne["nom"],'prenom'=>$ligne['prenom'],'filiere'=>$ligne['filiere'],'niveau'=>$ligne['niveau'],'classe'=>$ligne['classe'],'role'=>$ligne['role']];
    }
    return $etudiant;
}
function recupUtilByFiliere($db,$filiere){
    $requete=$db->prepare('SELECT * from utilisateur where filiere=?');
    $requete->execute(array($filiere));
    $etudiants=array();
    while($ligne=$requete->fetch(PDO::FETCH_ASSOC)){
        $etudiants[]=['id'=>$ligne['id_etudiant'],'nom'=>$ligne["nom"],'prenom'=>$ligne['prenom'],'filiere'=>$ligne['filiere'],'niveau'=>$ligne['niveau'],'classe'=>$ligne['classe'],'role'=>$ligne['role']];
    }
    return $etudiants;
}
function recupAdminByMail($db,$email){
    $requete=$db->prepare('SELECT * from administrateur where email=?');
    $requete->execute(array($email));
    while($ligne=$requete->fetch(PDO::FETCH_ASSOC)){
        $etudiant= ['id'=>$ligne['id_admin'],'nom'=>$ligne["nom"],'filiere'=>$ligne['filiere']];
    }
    return $etudiant;
}
function recupEtudiantByClass($db,$niveau,$filiere,$classe){
    $requete=$db->prepare('SELECT * from utilisateur where niveau=? and classe=? and filiere=?');
    $requete->execute(array($niveau,$classe,$filiere));
    $etudiants=array();
    while($ligne=$requete->fetch(PDO::FETCH_ASSOC)){
        $etudiants[]=['id'=>$ligne['id_etudiant'],'nom'=>$ligne["nom"],'prenom'=>$ligne['prenom'],'filiere'=>$ligne['filiere'],'niveau'=>$ligne['niveau'],'classe'=>$ligne['classe']];
    }
    return $etudiants;
}

function insertE($db,$email,$nom,$prenom,$password,$filiere,$classe,$niveau){
    $requete=$db->prepare('INSERT into utilisateur(nom,prenom,filiere,email,mot_de_passe,niveau,classe) VALUES(?,?,?,?,?,?,?)');
    $requete->execute(array($nom,$prenom,$filiere,$email,$password,$niveau,$classe));
}
function insertA($db,$date,$matiere,$periode,$id_etudiant){
    $requete=$db->prepare('INSERT into abscences(dates,periode,matiere,id_etudiant) values(?,?,?,?)');
    $requete->execute(array($date,$periode,$matiere,$id_etudiant));
}
function recupJustificatif($db,$id){
    $requete=$db->prepare('SELECT * from justificatifs where id_etudiant=?');
    $requete->execute(array($id));
    $justificatifs=array();
    while($ligne=$requete->fetch(PDO::FETCH_ASSOC)){
        $justificatifs[]=['id'=>$ligne['id_justificatif'],'date'=>$ligne["dates"],'motif'=>$ligne['motif'],'commentaire'=>$ligne['commentaire'],'file'=>$ligne['files'],'id_etudiant'=>$ligne['id_etudiant'],'statut'=>$ligne['statut']];
    }
    return $justificatifs;
}
function recupJustificatifByStatut($db,$statut,$id){
    $requete=$db->prepare('SELECT * from justificatifs where statut=? and id_etudiant=?'); 
    $requete->execute(array($statut,$id));
    $justificatifs=array();
    while($ligne=$requete->fetch(PDO::FETCH_ASSOC)){
        $justificatifs[]=['id'=>$ligne['id_justificatif'],'date'=>$ligne["dates"],'motif'=>$ligne['motif'],'commentaire'=>$ligne['commentaire'],'file'=>$ligne['files'],'id_etudiant'=>$ligne['id_etudiant'],'statut'=>$ligne['statut']];
    }
    return $justificatifs;
}
function insertJ($db,$id_etudiant,$date,$motif,$commentaire,$statut,$file,$periode,$date_absence){
    $requete=$db->prepare("INSERT Into justificatifs(dates,motif,commentaire,statut,files,id_etudiant,periode,date_abscence) values(?,?,?,?,?,?,?,?)");
    $requete->execute(array($date,$motif,$commentaire,$statut,$file,$id_etudiant,$periode,$date_absence));
}
function recupJustificatifByFilier($db,$filiere){
    $requete=$db->prepare('SELECT * FROM justificatifs, utilisateur WHERE justificatifs.id_etudiant = utilisateur.id_etudiant AND filiere=? AND statut="attente"');
    $requete->execute(array($filiere));
    $justificatifs=array();
    while($ligne=$requete->fetch(PDO::FETCH_ASSOC)){
        $justificatifs[]=['id'=>$ligne['id_justificatif'],'date'=>$ligne["dates"],'motif'=>$ligne['motif'],'commentaire'=>$ligne['commentaire'],'file'=>$ligne['files'],'nom'=>$ligne['nom'],'nom'=>$ligne['nom'],'prenom'=>$ligne['prenom'],'niveau'=>$ligne['niveau'],'classe'=>$ligne['classe'],'periode'=>$ligne['periode'],'dateA'=>$ligne['date_abscence'],'idE'=>$ligne['id_etudiant']];
    }
    return $justificatifs;
}
///methode pour implementer le filtre 
function filtrerUtilisateur($db,$classe,$niveau,$filiere){
    $etudiants=array();
    // if(empty($classe)||empty($niveau)||!empty($date)){
    //     $requete=$db->prepare('SELECT * from utilisateur,abscences where dates=? and filiere=?');
    //     $requete->execute(array($date,$filiere));
    //  while($ligne=$requete->fetch(PDO::FETCH_ASSOC)){
    //     $etudiants[]=['id'=>$ligne['id_etudiant'],'nom'=>$ligne["nom"],'prenom'=>$ligne['prenom'],'filiere'=>$ligne['filiere'],'niveau'=>$ligne['niveau'],'classe'=>$ligne['classe']];
    // }
     if(empty($classe) & !empty($niveau)){
        $requete=$db->prepare('SELECT * from utilisateur where niveau=? and filiere=?');
        $requete->execute(array($niveau,$filiere));
     while($ligne=$requete->fetch(PDO::FETCH_ASSOC)){
        $etudiants[]=['id'=>$ligne['id_etudiant'],'nom'=>$ligne["nom"],'prenom'=>$ligne['prenom'],'filiere'=>$ligne['filiere'],'niveau'=>$ligne['niveau'],'classe'=>$ligne['classe'],'role'=>$ligne['role']];
    }
    }else if(!empty($classe) & empty($niveau)){
        $requete=$db->prepare('SELECT * from utilisateur where classe=? and filiere=?');
        $requete->execute(array($classe,$filiere));
     while($ligne=$requete->fetch(PDO::FETCH_ASSOC)){
        $etudiants[]=['id'=>$ligne['id_etudiant'],'nom'=>$ligne["nom"],'prenom'=>$ligne['prenom'],'filiere'=>$ligne['filiere'],'niveau'=>$ligne['niveau'],'classe'=>$ligne['classe'],'role'=>$ligne['role']];
    }
    }else if(!empty($classe)& !empty($niveau)){
        $requete=$db->prepare('SELECT * from utilisateur where niveau=? and classe=? and filiere=?');
        $requete->execute(array($niveau,$classe,$filiere));
     while($ligne=$requete->fetch(PDO::FETCH_ASSOC)){
        $etudiants[]=['id'=>$ligne['id_etudiant'],'nom'=>$ligne["nom"],'prenom'=>$ligne['prenom'],'filiere'=>$ligne['filiere'],'niveau'=>$ligne['niveau'],'classe'=>$ligne['classe'],'role'=>$ligne['role']];
    }
    }
    else{
        $requete=$db->prepare('SELECT * from utilisateur where filiere=?');
        $requete->execute(array($filiere));
        $etudiants=array();
        while($ligne=$requete->fetch(PDO::FETCH_ASSOC)){
            $etudiants[]=['id'=>$ligne['id_etudiant'],'nom'=>$ligne["nom"],'prenom'=>$ligne['prenom'],'filiere'=>$ligne['filiere'],'niveau'=>$ligne['niveau'],'classe'=>$ligne['classe'],'role'=>$ligne['role']];
        }
    }
    return $etudiants;
}
function updateA($db,$id){
    $requete=$db->prepare("UPDATE absences set statut=? where id_justificatif=?");
}
function updateJus($db,$id,$statut){
    $requete=$db->prepare("UPDATE justificatifs set statut=? where id_justificatif=?");
    $requete->execute(array($statut,$id));
}
function countE($db){
    $requete=$db->prepare("SELECT * FROM utilisateur");
    $requete->execute();
    return $requete->rowCount();
}
function countJ($db){
    $requete=$db->prepare("SELECT * FROM justificatifs");
    $requete->execute();
    return $requete->rowCount();
}
function countJV($db,$chaine){
    $requete=$db->prepare("SELECT * FROM justificatifs where statut=?");
    $requete->execute(array($chaine));
    return $requete->rowCount();
}
function countH($db){
    $requete=$db->prepare("SELECT count(id_abscence) as coun FROM abscences");
    $requete->execute();
    while($ligne=$requete->fetch(PDO::FETCH_ASSOC)){
        $arr=['count'=>$ligne['coun']];
    }
    return $arr;
}

//  function rangePeriode($chaine){
//     $tabChaine=explode(',',$chaine);
//     return array_slice($tabChaine,1);
// }
function recupHeure($db,$id){
    $requete=$db->prepare("SELECT count(id_abscence) as coun FROM abscences where id_etudiant=?");
    $requete->execute(array($id));
    while($ligne=$requete->fetch(PDO::FETCH_ASSOC)){
        $arr=['count'=>$ligne['coun']];
    }
    return $arr;
}
function recupHeureJ($db,$id){
    $requete=$db->prepare("SELECT count(id_abscence) as coun FROM abscences where id_etudiant=? and statut=?");
    $requete->execute(array($id,'justifier'));
    while($ligne=$requete->fetch(PDO::FETCH_ASSOC)){
        $arr=['count'=>$ligne['coun']];
    }
    return $arr;
}
function recupMatiere($db){
    $requete=$db->prepare('SELECT * FROM matieres');
    $requete->execute();
    $matieres=array();
    while($ligne=$requete->fetch(PDO::FETCH_ASSOC)){
        $matieres[]=['nom'=>$ligne['nom_matiere'],'emailP'=>$ligne['emailProf']];
    }
    return $matieres;
}
function recupMailOfMatiere($db,$matiere){
    $requete=$db->prepare("SELECT *  FROM matieres where matiere=?");
    $requete->execute(array($matiere));
    while($ligne=$requete->fetch(PDO::FETCH_ASSOC)){
        $email=$ligne['emailprof'];
    }
    return $email;
}
function recupUtilInfo($db,$id){
    $requete=$db->prepare('SELECT * from abscences where id_etudiant=?');
    $requete->execute(array($id));
    $absences=array();
    while($ligne=$requete->fetch(PDO::FETCH_ASSOC)){
        $absences[]= ['date'=>$ligne['dates'],'periode'=>$ligne["periode"],'matiere'=>$ligne['matiere'],'statut'=>$ligne['statut']];
    }
    return $absences;
}
function supprimerUtilisateur($db,$id){
    $requete=$db->prepare('DELETE from utilisateur where id_etudiant=?');
    $requete->execute(array($id));
}
function recupJustificatifTraiter($db,$filiere){
    $requete=$db->prepare('SELECT * FROM justificatifs, utilisateur WHERE justificatifs.id_etudiant = utilisateur.id_etudiant AND filiere=? and statut="valider" or statut="rejete"');
    $requete->execute(array($filiere));
    $justificatifs=array();
    while($ligne=$requete->fetch(PDO::FETCH_ASSOC)){
        $justificatifs[]=['id'=>$ligne['id_justificatif'],'date'=>$ligne["dates"],'motif'=>$ligne['motif'],'commentaire'=>$ligne['commentaire'],'file'=>$ligne['files'],'nom'=>$ligne['nom'],'nom'=>$ligne['nom'],'prenom'=>$ligne['prenom'],'niveau'=>$ligne['niveau'],'classe'=>$ligne['classe'],'periode'=>$ligne['periode'],'dateA'=>$ligne['date_abscence'],'idE'=>$ligne['id_etudiant'],'statut'=>$ligne['statut']];
    }
    return $justificatifs;
}
?>
