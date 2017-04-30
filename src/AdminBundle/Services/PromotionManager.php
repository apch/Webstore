<?php

namespace AdminBundle\Services;

use AdminBundle\Entity\Category;
use AdminBundle\Entity\Product;
use AdminBundle\Repository\PromotionRepository;

class PromotionManager
{
    protected $general_promotion;

    protected $category_promotions;

    protected $product_promotions;
    /**
     * PriceCalculator constructor.
     *
     * @param PromotionRepository $repo
     */
    public function __construct(PromotionRepository $repo)
    {
        $this->general_promotion =  $repo->fetchBiggestGeneralPromotion();
        $this->category_promotions = $repo->fetchCategoriesPromotions();
        $this->product_promotions = $repo->fetchProductsPromotions();
    }


    /**
     * @return int
     */
    public function getGeneralPromotion()
    {
        return $this->general_promotion ?? 0;
    }

    /**
     * @param Category $category
     *
     * @return bool
     */
    public function hasCategoryPromotion($category)
    {
        return array_key_exists($category->getId(), $this->category_promotions);
    }

    /**
     * @param Category $category
     *
     * @return int
     */
    public function getCategoryPromotion($category)
    {
        return $this->category_promotions[$category->getId()];
    }

    /**
     * @param Product $product
     *
     * @return bool
     */
    public function hasProductPromotion($product)
    {
        return array_key_exists($product->getId(), $this->product_promotions);
    }

    /**
     * @param Product $product
     *
     * @return int
     */
    public function getProductPromotion($product)
    {
        return $this->product_promotions[$product->getId()];
    }
}
