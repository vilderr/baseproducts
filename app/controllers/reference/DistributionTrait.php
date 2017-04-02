<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 02.04.17
 * Time: 21:56
 */

namespace app\controllers\reference;

/**
 * Class DistributionTrait
 * @package app\controllers\reference
 */
trait DistributionTrait
{
    public function actionDistribution($type, $reference_id)
    {
        $reference = $this->findReferenceByType($type, $reference_id);

        return $this->render('distribution', [
            'reference' => $reference,
        ]);
    }
}