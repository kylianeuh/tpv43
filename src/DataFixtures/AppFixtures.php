<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use GuzzleHttp\Client;

use App\Entity\Catalogue\Livre;
use App\Entity\Catalogue\Musique;
use App\Entity\Catalogue\Film;
use App\Entity\Catalogue\Piste;

use Psr\Log\LoggerInterface;

class AppFixtures extends Fixture
{
	protected $logger;

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }
	
    public function load(ObjectManager $manager): void
    {
		if (count($manager->getRepository("App\Entity\Catalogue\Article")->findAll()) == 0) {
			$ebay = new Ebay($this->logger);
			//$ebay->setCategory('CDs');
			//$keywords = 'Ibrahim Maalouf' ;
			$ebay->setCategory('Films');
			$keywords = 'avatar' ;

			$itemSummaries = $ebay->searchItemSummaries($keywords, 20);
			
			if ($itemSummaries !== false) {
				foreach ($itemSummaries as $itemSummary) {
					// https://developer.ebay.com/api-docs/buy/browse/resources/item/methods/getItem
					// $itemSummary["itemId"] = "v1|167503443270|0"
					$id = explode("|", $itemSummary["itemId"])[1] ;
					if ($ebay->categoryInCategories('Livres', $itemSummary["categories"])) {
						$entityLivre = new Livre();
						$entityLivre->setId((int) $id);
						$entityLivre->setTitre($itemSummary["title"]);
						$entityLivre->setAuteur($ebay->getItem("Auteur", $id));
						$entityLivre->setISBN( $ebay->getItem("ISBN", $id));
						$entityLivre->setDateDePublication( $ebay->getItem("Année de publication", $id));
						$entityLivre->setPrix((float) $itemSummary["price"]["value"]);
						$entityLivre->setDisponibilite(1);
						$entityLivre->setImage($itemSummary["image"]["imageUrl"]);
						$manager->persist($entityLivre);
						$manager->flush();
					}
					if ($ebay->categoryInCategories('Films', $itemSummary["categories"])) {
						$entityFilm = new Film();
						$entityFilm->setId((int) $id);
						$entityFilm->setTitre($itemSummary["title"]);
						$entityFilm->setRealisateur($ebay->getItem("realisateur", $id));
						$entityFilm->setIdTMDB( $ebay->getItem("ID TMDB", $id));
						$dureeValue = $ebay->getItem("durée du film", $id);
						$entityFilm->setDuree($dureeValue !== null ? (int) $dureeValue : null);
						$entityFilm->setDateDePublication( $ebay->getItem("Année de publication", $id));
						$entityFilm->setPrix((float) $itemSummary["price"]["value"]);
						$entityFilm->setDisponibilite(1);
						$entityFilm->setImage($itemSummary["image"]["imageUrl"]);
						$manager->persist($entityFilm);
						$manager->flush();
					}
					if ($ebay->categoryInCategories('CDs', $itemSummary["categories"])) {
						$entityMusique = new Musique();
						$entityMusique->setId((int) $id);
						$entityMusique->setTitre($itemSummary["title"]);
						$entityMusique->setArtiste($ebay->getItem("Artiste", $id));
						$entityMusique->setDateDeSortie( $ebay->getItem("Année de sortie", $id));
						$entityMusique->setPrix((float) $itemSummary["price"]["value"]);
						$entityMusique->setDisponibilite(1);
						$entityMusique->setImage($itemSummary["image"]["imageUrl"]);
						if (!isset($albums)) {
							$spotify = new Spotify($this->logger);
							$albums = $spotify->searchAlbumsByArtist($keywords) ;
						}
						$j = 0 ;
						$sortir = ($j==count($albums)) ;
						$albumTrouve = false ;
						while (!$sortir) {
							$wordsAlbum = $this->extractWords(mb_strtolower($albums[$j]->name));
							$wordsTitle = $this->extractWords(mb_strtolower($entityMusique->getTitre()));
							$communs = array_intersect($wordsAlbum, $wordsTitle); // Find common words
							$communs = array_unique($communs); // Remove duplicates
							if(count($communs) > 0) {
								$titreCommunEbay = implode("", $communs) ;								
								$titreSpotify = str_replace(" ","",mb_strtolower($albums[$j]->name)) ;
								$albumTrouve = ($titreSpotify == $titreCommunEbay) ;
							}
							$j++ ;
							$sortir = $albumTrouve || ($j==count($albums)) ;
						}
						if ($albumTrouve) {
							$tracks = $spotify->searchTracksByAlbum($albums[$j-1]->id) ;
							foreach ($tracks as $track) {
								$entityPiste = new Piste();
								$entityPiste->setTitre($track->name);
								$entityPiste->setMp3($track->preview_url);
								$manager->persist($entityPiste);
								$manager->flush();
								$entityMusique->addPiste($entityPiste) ;
							}
						}
						$manager->persist($entityMusique);
						$manager->flush();
					}
				}
			}
			$entityLivre = new Livre();
			$entityLivre->setId(55677821);
			$entityLivre->setTitre("Le seigneur des anneaux");
			$entityLivre->setAuteur("J.R.R. TOLKIEN");
			$entityLivre->setISBN("2075134049");
			$entityLivre->setNbPages(736);
			$entityLivre->setDateDePublication("03/10/19");
			$entityLivre->setPrix("8.90");
			$entityLivre->setDisponibilite(1);
			$entityLivre->setImage("images/51O0yBHs+OL._SL140_.jpg");
			$manager->persist($entityLivre);
			$entityLivre = new Livre();
			$entityLivre->setId(55897821);
			$entityLivre->setTitre("Un paradis trompeur");
			$entityLivre->setAuteur("Henning Mankell");
			$entityLivre->setISBN("275784797X");
			$entityLivre->setNbPages(400);
			$entityLivre->setDateDePublication("09/10/14");
			$entityLivre->setPrix("6.80");
			$entityLivre->setDisponibilite(1);
			$entityLivre->setImage("images/71uwoF4hncL._SL140_.jpg");
			$manager->persist($entityLivre);
			$entityLivre = new Livre();
			$entityLivre->setId(56299459);
			$entityLivre->setTitre("Dôme tome 1");
			$entityLivre->setAuteur("Stephen King");
			$entityLivre->setISBN("2212110685");
			$entityLivre->setNbPages(840);
			$entityLivre->setDateDePublication("06/03/13");
			$entityLivre->setPrix("8.90");
			$entityLivre->setDisponibilite(1);
			$entityLivre->setImage("images/719FffADQAL._SL140_.jpg");
			$manager->persist($entityLivre);
			$manager->flush();
		}
    }
	
	public function extractWords(string $text): array
	{
		$text = mb_strtolower($text); // Convert to lowercase
		$text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);  // Remove punctuation
		$words = preg_split('/\s+/', trim($text)); // Split into words

		return $words;
	}
}
