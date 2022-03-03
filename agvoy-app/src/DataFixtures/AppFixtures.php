<?php

namespace App\DataFixtures;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Owner;
use App\Entity\Region;
use App\Entity\Room;
use App\Entity\User;

class AppFixtures extends Fixture
{

    // définit un nom de référence pour une instance de Region
    public const IDF_REGION_REFERENCE = 'idf-region';
    public const TEXAS_REGION_REFERENCE = 'texas-region';
    public const PDLL_REGION_REFERENCE = 'pddl-region';

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
    $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {

    $region_idf = new Region();
    $region_idf->setCountry("FR");
    $region_idf->setName("Ile de France");
    $region_idf->setPresentation("La région française capitale");
    $manager->persist($region_idf);

    $manager->flush();

    $region_texas = new Region();
    $region_texas->setCountry("US");
    $region_texas->setName("Texas");
    $region_texas->setPresentation("La plus grande région des Etats-Unis");
    $manager->persist($region_texas);

    $manager->flush();

    $region_pdll = new Region();
    $region_pdll->setCountry("FR");
    $region_pdll->setName("Pays de la Loire");
    $region_pdll->setPresentation("Region à l'Ouest de la France, presque la Bretagne mais en vrai non");
    $manager->persist($region_pdll);

    $manager->flush();
    // Une fois l'instance de region_idf sauvée en base de données,
    // elle dispose d'un identifiant généré par Doctrine, et peut
    // donc être sauvegardée comme future référence.
    $this->addReference(self::IDF_REGION_REFERENCE, $region_idf);
    $this->addReference(self::TEXAS_REGION_REFERENCE, $region_texas);
    $this->addReference(self::PDLL_REGION_REFERENCE, $region_pdll);

    $owner = new Owner();
    $owner->setFirstname("Tristan");
    $owner->setFamilyName("Amadei");
    $owner->setCountry("FR");
    $manager->persist($owner);

    $manager->flush();

    $owner_geralt = new Owner();
    $owner_geralt->setFirstname("Geralt");
    $owner_geralt->setFamilyName("de Riv");
    $owner_geralt->setCountry("US");
    $manager->persist($owner_geralt);

    $manager->flush();

    $room = new Room();
    $room->setSummary("Beau poulailler ancien à Évry");
    $room->setDescription("très joli espace sur paille");
    $room->setCapacity(4);
    $room->setSuperficy(50);
    $room->setPrice(20);
    $room->setAddress("Evry");
    //$room->addRegion($region);
    // On peut plutôt faire une référence explicite à la référence
    // enregistrée précédamment, ce qui permet d'éviter de se
    // tromper d'instance de Region :
    $room->addRegion($this->getReference(self::IDF_REGION_REFERENCE));
    $room->setOwner($owner);   
    $room->setImageName("");  
    $room->setImageFile();
    $manager->persist($room);

    $manager->flush();

    $room_disney = new Room();
    $room_disney->setSummary("DisneyLand");
    $room_disney->setDescription("Petit parc tranquille de 7km2. Attention, il y a souvent du monde. N'hésitez pas à les chasser, ils ne sont pas chez eux et n'ont rien à faire ici.");
    $room_disney->setCapacity(28000);
    $room_disney->setSuperficy(700000000);
    $room_disney->setPrice(370000);
    $room_disney->setAddress("Bd de Parc, 77000 Coupvray");
    $room_disney->addRegion($this->getReference(self::IDF_REGION_REFERENCE));
    $room_disney->setOwner($owner);   
    $room_disney->setImageName("484853-visuels-disneyland-paris-chateau");
    $room_disney->setImageFile(null);  
    $manager->persist($room_disney);

    $manager->flush();

    $room_km = new Room();
    $room_km->setSummary("Kaer Morhen");
    $room_km->setDescription("Grand chateau de sorceleurs. Il peut rester quelques bouts de monstres par-ci par-la mais si on laisse cela de côté, et qu'on ne prend pas en compte les parties du château ébranlées, la demeure est charmante.");
    $room_km->setCapacity(50);
    $room_km->setSuperficy(2700);
    $room_km->setPrice(45);
    $room_km->setAddress("Montagnes du royaume de Kaedwen");
    $room_km->addRegion($this->getReference(self::TEXAS_REGION_REFERENCE));
    $room_km->setOwner($owner_geralt);     
    $manager->persist($room_km);

    $manager->flush();

    foreach ($this->getUserData() as [$email,$plainPassword,$role]) {
        $user = new User();
        $encodedPassword = $this->passwordEncoder->encodePassword($user, $plainPassword);
        $user->setEmail($email);
        $user->setPassword($encodedPassword);

        $roles = array();
        $roles[] = $role;
        $user->setRoles($roles);

        if ($role == 'ROLE_OWNER') {
            if ($plainPassword == 'tristan') {
                $user->setOwner($owner);
            } else {
                $user->setOwner($owner_geralt);
            }
        }

        $manager->persist($user);
    }
    $manager->flush();

    }

    private function getUserData()
    {
    yield ['admin@mail.com','admin','ROLE_ADMIN'];
    yield ['tristan@mail.com','tristan','ROLE_OWNER'];
    yield ['geralt@mail.com','geralt','ROLE_OWNER'];
    yield ['user@mail.com', 'user', 'ROLE_USER'];

    }
}