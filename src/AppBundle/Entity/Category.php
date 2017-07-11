<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 */
class Category
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255)
     */
    private $path;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Media", mappedBy="category")
     */
    private $medias;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Vote", mappedBy="category")
     */
    private $votes;

    /**
     *
     */
    public function __construct(){
        $this->medias = new ArrayCollection();
        $this->votes = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return Category
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param Media $media
     *
     * @return $this
     */
    public function addMedia(Media $media){
        $this->medias->add($media);

        $media->setCategory($this);

        return $this;
    }

    /**
     * @param Media $media
     *
     * @return $this
     */
    public function removeMedia(Media $media){
        $this->medias->removeElement($media);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getMedias(){
        return $this->medias;
    }

    /**
     * @return string
     */
    public function getImagePath(){
        return $this->getPath() . '.jpg';
    }

    /**
     * @param Vote $vote
     *
     * @return $this
     */
    public function addVote(Vote $vote){
        $this->votes->add($vote);

        $vote->setCategory($this);

        return $this;
    }

    /**
     * @param Vote $vote
     *
     * @return $this
     */
    public function removeVote(Vote $vote){
        $this->votes->removeElement($vote);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getVotes(){
        return $this->votes;
    }

    /**
     * @return string
     */
    public function __toString(){
        return $this->name;
    }
}

