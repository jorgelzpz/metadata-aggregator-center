<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Validator\Constraints as Assert;

//Request::setTrustedProxies(array('127.0.0.1'));
$private = $app['controllers_factory'];

$main = $private->get('/', function () use ($app) {
    $sets = $app['sets.repository']->findAll();

    return $app['twig']->render('list.html.twig', [
        'sets' => $sets,
    ]);
})
->bind('main')
;

$edit = $private->match('/edit/{name}', function (Request $request, $name) use ($app) {
    $set = null;
    if (!empty($name)) {
        $set = $app['sets.repository']->findByName($name);
    }

    $form = $app['form.factory']
        ->createBuilder(RedIRIS\MetadataCenter\MetadataSetType::class, $set)
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $set = $form->getData();
        $app['sets.repository']->save($set);

        return $app->redirect(
            $app['url_generator']->generate('main')
        );
    }

    return $app['twig']->render('form-set.html.twig', [
        'form' => $form->createView(),
    ]);
})
->value('name', '')
->bind('edit-set');

$remove = $private->match('/remove/{name}', function (Request $request, $name) use ($app) {
    $set = $app['sets.repository']->findByName($name);

    $form = $app['form.factory']->createBuilder(FormType::class, $set)
        ->add('id', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class, [])
        ->add('confirm', \Symfony\Component\Form\Extension\Core\Type\CheckboxType::class, [
            'label' => 'Sí, estoy seguro',
            'mapped' => false,
        ])
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $set = $form->getData();
        $app['sets.repository']->delete($set);

        return $app->redirect(
            $app['url_generator']->generate('main')
        );
    }

    return $app['twig']->render('confirm-removal.html.twig', [
        'set' => $set,
        'form' => $form->createView(),
    ]);
})
->bind('remove-set');

$public = $app['controllers_factory'];

$sets = $public->get('/sets', function () use ($app) {
    $sets = $app['sets.repository']->findAll();

    $result = [];
    // TODO use transformer?
    foreach ($sets as $set) {
        $result[] = [
            'name' => $set->getName(),
            'url' => $app['url_generator']->generate(
                'download-set',
                [
                    'name' => $set->getName(),
                ]
            ),
        ];
    }

    return new JsonResponse([
        'sets' => $result,
    ]);
})
->bind('sets')
;

$download = $public->get('/download/{name}', function ($name) use ($app) {
    $set = $app['sets.repository']->findByName($name);

    $path = $app['metadata_output_dir'] . '/' . basename($name) . '.xml';
    if (!is_readable($path)) {
        $app['monolog']->warning(
            sprintf('Tried to download set %s, but it is not available yet', $name)
        );
        $app->abort(202, 'Metadata not ready yet');
    }

    $response = new BinaryFileResponse($path);
    $response->headers->set('Content-Type', 'application/samlmetadata+xml');

    return $response;
})->bind('download-set');

$private->before(function(Request $request, Silex\Application $app) {
    require_once $app['ssp_directory'] . '/lib/_autoload.php';
    $as = new SimpleSAML_Auth_Simple($app['ssp_sp']);
    $as->requireAuth();
});

$app->mount('/', $private);
$app->mount('/public', $public);

$app->get('/logout', function (Request $request) use ($app) {
    require_once $app['ssp_directory'] . '/lib/_autoload.php';
    $as = new SimpleSAML_Auth_Simple($app['ssp_sp']);
    if ($as->isAuthenticated()) {
        $as->logout();
    } else {
        return new Response("Bye!");
    }
})->bind('logout');


$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/'.$code.'.html.twig',
        'errors/'.substr($code, 0, 2).'x.html.twig',
        'errors/'.substr($code, 0, 1).'xx.html.twig',
        'errors/default.html.twig',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});
