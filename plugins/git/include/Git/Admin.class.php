<?php
/**
 * Copyright (c) Enalean, 2012 - 2014. All Rights Reserved.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'common/include/CSRFSynchronizerToken.class.php';

/**
 * This handles site admin part of Git
 */
class Git_Admin {
    
    private $servers;

    /** @var Git_RemoteServer_GerritServerFactory */
    private $gerrit_server_factory;

    /** @var CSRFSynchronizerToken */
    private $csrf;

    public function __construct(Git_RemoteServer_GerritServerFactory $gerrit_server_factory, CSRFSynchronizerToken $csrf) {
        $this->gerrit_server_factory = $gerrit_server_factory;
        $this->csrf                  = $csrf;
    }

    public function process(Codendi_Request $request) {
        $request_gerrit_servers = $request->get('gerrit_servers');
        if (is_array($request_gerrit_servers)) {
            $this->csrf->check();
            $this->fetchGerritServers();
            $this->updateServers($request_gerrit_servers);
            $GLOBALS['Response']->redirect('/plugins/git/admin/');
        }
    }

    public function display() {
        $title    = $GLOBALS['Language']->getText('plugin_git', 'descriptor_name');
        $renderer = TemplateRendererFactory::build()->getRenderer(dirname(GIT_BASE_DIR).'/templates');

        $admin_presenter = new Git_AdminPresenter(
            $title,
            $this->csrf,
            $this->getListOfServersPresenters()
        );

        $GLOBALS['HTML']->header(array('title' => $title, 'selected_top_tab' => 'admin'));
        $renderer->renderToPage('admin-plugin', $admin_presenter);
        $GLOBALS['HTML']->footer(array());
    }

    private function fetchGerritServers() {
        if (empty($this->servers)) {
            $this->servers = $this->gerrit_server_factory->getServers();
        }
 
        $this->servers["0"] = new Git_RemoteServer_GerritServer(0, '', '', '', '', '', '', false, Git_RemoteServer_GerritServer::DEFAULT_GERRIT_VERSION, '');
    }

    private function getListOfServersPresenters() {
        $this->fetchGerritServers();

        $list_of_presenters = array();
        foreach ($this->servers as $server) {
            $is_used = $this->gerrit_server_factory->isServerUsed($server);
            $list_of_presenters[] = new Git_RemoteServer_GerritServerPresenter($server, $is_used);
        }

        return $list_of_presenters;
    }

    private function updateServers(array $request_gerrit_servers) {
        foreach ($request_gerrit_servers as $id => $settings) {
            $server = $this->servers[$id];

            if (empty($server)) {
                continue;
            }
            if (! empty($settings['delete'])) {
                $this->gerrit_server_factory->delete($server);
                unset($this->servers[$id]);
                continue;
            }

            $host                   = isset($settings['host'])              ? $settings['host']             : '';
            $ssh_port               = isset($settings['ssh_port'])          ? $settings['ssh_port']         : '';
            $http_port              = isset($settings['http_port'])         ? $settings['http_port']        : '';
            $login                  = isset($settings['login'])             ? $settings['login']            : '';
            $identity_file          = isset($settings['identity_file'])     ? $settings['identity_file']    : '';
            $replication_ssh_key    = isset($settings['replication_key'])   ? $settings['replication_key']  : '';
            $use_ssl                = isset($settings['use_ssl'])                                               ;
            $gerrit_version         = isset($settings['gerrit_version'])    ? $settings['gerrit_version']   : '';
            $http_password          = isset($settings['http_password'] )    ? $settings['http_password']    : '';

            if ($host !== '' &&
                ($host != $server->getHost() ||
                $ssh_port != $server->getSSHPort() ||
                $http_port != $server->getHTTPPort() ||
                $login != $server->getLogin() ||
                $identity_file != $server->getIdentityFile() ||
                $replication_ssh_key != $server->getReplicationKey() ||
                $use_ssl != $server->usesSSL() ||
                $gerrit_version != $server->getGerritVersion() ||
                $http_password != $server->getHTTPPassword())
            ) {
                $server
                    ->setHost($host)
                    ->setSSHPort($ssh_port)
                    ->setHTTPPort($http_port)
                    ->setLogin($login)
                    ->setIdentityFile($identity_file)
                    ->setReplicationKey($replication_ssh_key)
                    ->setUseSSL($use_ssl)
                    ->setGerritVersion($gerrit_version)
                    ->setHTTPPassword($http_password);
                $this->gerrit_server_factory->save($server);
                $this->servers[$server->getId()] = $server;
            }
        }
    }
}