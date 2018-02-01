<?php
/**
 * @copyright Copyright (c) 2018, Roeland Jago Douma <roeland@famdouma.nl>
 *
 * @author Roeland Jago Douma <roeland@famdouma.nl>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
namespace OCA\AppDataCleaner\Command;

use OC\Files\AppData\Factory;
use OCP\Files\IAppData;
use OCP\Files\NotFoundException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanJS extends Command {
	/** @var IAppData */
	private $appData;

	public function __construct(Factory $appDataFactory) {
		parent::__construct();

		$this->appData = $appDataFactory->get('js');
	}

	public function configure() {
		$this
			->setName('appdatacleaner:js')
			->setDescription('Cleanup cached js files')
			->addArgument(
				'app',
				InputArgument::OPTIONAL,
				'Only delete for this app'
			);
	}

	public function execute(InputInterface $input, OutputInterface $output) {
		$app = $input->getArgument('app');

		if ($app === null) {
			$dirs = $this->appData->getDirectoryListing();
		} else {
			try {
				$dirs = [$this->appData->getFolder($app)];
			} catch (NotFoundException $e) {
				return;
			}
		}

		foreach ($dirs as $dir) {
			$files = $dir->getDirectoryListing();
			foreach ($files as $file) {
				$file->delete();
			}
		}
	}
}