<?php

namespace AdminBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Product
 *
 * @ORM\Table(name="products")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\ProductRepository")
 */
class Product
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
     * @ORM\Column(name="price", type="decimal", precision=10, scale=2)
     *
     * @Assert\NotBlank()
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     *
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     *
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="quantity", type="integer")
     *
     * @Assert\NotBlank()
     */
    private $quantity;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     *
     */
    private $createdOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_on", type="datetime")
     */
    private $updatedOn;


    /**
     * @ORM\Column(name="image_url", type="string")
     *
     * @Assert\NotBlank(message="Please, upload image as JPG file")
     * @Assert\File(mimeTypes={"image/jpeg"})
     */
    private $imageUrl;


    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=false)
     */
    private $deleted;


    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AdminBundle\Entity\Category")
     * @ORM\JoinTable(name="products_categories",
     *      joinColumns = {@ORM\JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="id")}
     *     )
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\Promotion", mappedBy="products")
     */
    private $promotions;

    /**
     * @ORM\OneToOne(targetEntity="Featured", mappedBy="product")
     */
    private $featured;


    public function __construct()
    {
        $this->createdOn = new \DateTime();
        $this->updatedOn = $this->createdOn;
        $this->categories = new ArrayCollection();
        $this->productOrders = new ArrayCollection();
        $this->deleted = false;
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
     * Set price
     *
     * @param string $price
     *
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Product
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
     * Set description
     *
     * @param string $description
     *
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }


    /**
     * @param string $quantity
     *
     * @return string
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }



    /**
     * @return string
     */
    public function getQuantity()
    {
        return $this->quantity;
    }


    /**
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * @param \DateTime $createdOn
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }

    /**
     * @return mixed
     */
    public function getUpdatedOn()
    {
        return $this->updatedOn;
    }

    /**
     * @param mixed $updatedOn
     * @return Product
     */
    public function setUpdatedOn($updatedOn)
    {
        $this->updatedOn = $updatedOn;

        return $this;
    }




    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    /**
     * Add productOrders
     *
     * @param \AdminBundle\Entity\OrderProduct $productOrders
     * @return Product
     */
    public function addProductOrder(\AdminBundle\Entity\OrderProduct $productOrders)
    {
        $this->productOrders[] = $productOrders;

        return $this;
    }

    /**
     * Remove productOrders
     *
     * @param \AdminBundle\Entity\OrderProduct $productOrders
     */
    public function removeProductOrder(\AdminBundle\Entity\OrderProduct $productOrders)
    {
        $this->productOrders->removeElement($productOrders);
    }

    /**
     * Get productOrders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductOrders()
    {
        return $this->productOrders;
    }

    /**
     * @return ArrayCollection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param mixed $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    /**
     * Set featured
     *
     * @param \AdminBundle\Entity\Featured $featured
     * @return Product
     */
    public function setFeatured(\AdminBundle\Entity\Featured $featured = null)
    {
        $this->featured = $featured;

        return $this;
    }

    /**
     * Get featured
     *
     * @return \AdminBundle\Entity\Featured
     */
    public function getFeatured()
    {
        return $this->featured;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Product
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

}

