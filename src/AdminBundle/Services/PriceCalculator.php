<?php

namespace AdminBundle\Services;

use Doctrine\ORM\EntityManager;
use AdminBundle\Entity\Product;
use AdminBundle\Repository\PromotionRepository;

class PriceCalculator
{
    /** @var  PromotionManager */
    protected $manager;

    public function __construct(PromotionManager $manager) {
        $this->manager= $manager;
    }


    /**
     * @param Product $product
     *
     * @return float
     */
    public function calculate($product)
    {
        $categories    = $product->getCategories();

        $generalPromotion = $this->manager->getGeneralPromotion();
        $categoryPromotion = 0;
        $productPromotion = 0;
        $userPromotion = 0;
        foreach ($categories as $category){
            if($this->manager->hasCategoryPromotion($category)){
                $categoryPromotion = $this->manager->getCategoryPromotion($category);
            }
        }

        if($this->manager->hasProductPromotion($product)){
                $productPromotion = $this->manager->getProductPromotion($product);

        }

        $promotion = max($generalPromotion, $categoryPromotion, $productPromotion, $userPromotion);

        return $product->getPrice() - $product->getPrice() * ($promotion / 100);
    }
}
