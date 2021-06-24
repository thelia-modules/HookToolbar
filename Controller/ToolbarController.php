<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace HookToolbar\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Event\Cache\CacheEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Model\ConfigQuery;

/**
 * @author Franck Allimant <franck@cqfdev.fr>
 * @Route("/admin-toolbar", name="admin_toolbar")
 */
class ToolbarController extends BaseAdminController
{
    protected function doFlushCache($dir, EventDispatcherInterface $eventDispatcher) {

        if (null !== $result = $this->checkAuth(AdminResources::ADVANCED_CONFIGURATION, [], AccessManager::UPDATE)) {
            return $result;
        }

        $event = new CacheEvent($dir);

        $eventDispatcher->dispatch($event, TheliaEvents::CACHE_CLEAR);

        return null;
    }

    /**
     * @Route("/clear-cache/internal", name="_clear_internal_cache", methods="GET")
     */
    public function flushInternalAction(EventDispatcherInterface $eventDispatcher, $kernelCacheDir)
    {
       return $this->doFlushCache($kernelCacheDir, $eventDispatcher);
    }

    /**
     * @Route("/clear-cache/assets", name="_clear_assets_cache", methods="GET")
     */
    public function flushAssetsAction(EventDispatcherInterface $eventDispatcher)
    {
        return $this->doFlushCache(THELIA_WEB_DIR . "assets", $eventDispatcher);
    }

    /**
     * @Route("/clear-cache/files", name="_clear_files_cache", methods="GET")
     */
    public function flushImagesAndDocumentsAction(EventDispatcherInterface $eventDispatcher)
    {
        $this->doFlushCache(THELIA_WEB_DIR . ConfigQuery::read('image_cache_dir_from_web_root', 'cache'), $eventDispatcher);

        return $this->doFlushCache(THELIA_WEB_DIR . ConfigQuery::read('document_cache_dir_from_web_root', 'cache'), $eventDispatcher);
    }
}