<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Product controller.
 *
 * @Route("product")
 */
class ProductController extends Controller
{
    /**
     * Lists all product entities.
     *
     * @Route("/", name="product_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        // $em = $this->getDoctrine()->getManager();
        // // $products = $em->getRepository('AppBundle:Product')->findAll();
        // $dql = "SELECT p.id, p.name, count(b.id) AS openBugs FROM AppBundle:Bug b ".
        //        "JOIN b.products p WHERE b.status = 'OPEN' GROUP BY p.id";
        // $products = $em->createQuery($dql)->getScalarResult();
        // // 以下の書き方でも getScalarResult() と同様の結果になります
        // // $products = $em->createQuery($dql)
        // //     ->setHydrationMode(Query::HYDRATE_SCALAR)->getResult();


        $products = $this->getDoctrine()->getRepository('AppBundle:Bug')
            ->getOpenBugsByProductQuery()
            ->getResult();

        dump($products);

        return $this->render('product/index.html.twig', array(
            'products' => $products,
        ));
    }

    /**
     * Creates a new product entity.
     *
     * @Route("/new", name="product_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $product = new Product();                     // Productオブジェクトを作成
        $form = $this->createForm('AppBundle\Form\ProductType', $product);
        $form->handleRequest($request);
        // handleRequest()の中ではProductオブジェクトにPOSTパラメータがセットされ
        // 以下のコードと同様の処理が行われています。
        //
        // $params = $request->request->get('product');
        // $product->setName($params['name']);        // Productオブジェクトに値をセット

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager(); // DoctrineからEntityManagerを取得
            $em->persist($product);                   // EntityManagerの管理下に新規Productオブジェクトを追加
            $em->flush();                             // DBトランザクション実行

            return $this->redirectToRoute('product_show', array('id' => $product->getId()));
        }

        return $this->render('product/new.html.twig', array(
            'product' => $product,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a product entity.
     *
     * @Route("/{id}", name="product_show")
     * @Method("GET")
     */
    public function showAction(Product $product)
    {
        $deleteForm = $this->createDeleteForm($product);

        return $this->render('product/show.html.twig', array(
            'product' => $product,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing product entity.
     *
     * @Route("/{id}/edit", name="product_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Product $product)
    {
        $deleteForm = $this->createDeleteForm($product);
        $editForm = $this->createForm('AppBundle\Form\ProductType', $product);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('product_edit', array('id' => $product->getId()));
        }

        return $this->render('product/edit.html.twig', array(
            'product' => $product,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a product entity.
     *
     * @Route("/{id}", name="product_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Product $product)
    {
        $form = $this->createDeleteForm($product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();
        }

        return $this->redirectToRoute('product_index');
    }

    /**
     * Creates a form to delete a product entity.
     *
     * @param Product $product The product entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Product $product)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('product_delete', array('id' => $product->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
