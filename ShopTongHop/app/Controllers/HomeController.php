<?php
/**
 * ShopXpress - HomeController
 * Điều phối dữ liệu hiển thị Trang chủ Storefront
 */

class HomeController extends Controller {

    public function index(): void {
        $bannerModel = $this->model('Banner');
        $categoryModel = $this->model('Category');
        $flashSaleModel = $this->model('FlashSale');
        $productModel = $this->model('Product');

        $banners = $bannerModel->getActive();
        $categories = $categoryModel->getParents();
        $flashSale = $flashSaleModel->getActive();
        $featuredProducts = $productModel->getFeatured(8);
        $newArrivals = $productModel->getNewArrivals(8);

        $this->view('home/index', [
            'pageTitle'        => APP_NAME . ' - ' . APP_SLOGAN,
            'banners'          => $banners,
            'categories'       => $categories,
            'flashSale'        => $flashSale,
            'featuredProducts' => $featuredProducts,
            'newArrivals'      => $newArrivals,
        ]);
    }
}
