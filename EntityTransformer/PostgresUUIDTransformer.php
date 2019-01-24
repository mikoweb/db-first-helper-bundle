<?php

/*
 * (c) Rafał Mikołajun <root@rmweb.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mikoweb\Bundle\DbFirstHelperBundle\EntityTransformer;

final class PostgresUUIDTransformer implements EntityTransformerInterface
{
    /**
     * @var string
     */
    private $code;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function transform(): string
    {
        return str_replace(" = 'uuid_generate_v4()'", '', $this->code);
    }
}
