<?php

namespace LanSuite\Module\Foodcenter;

/**
 * Class ProductList
 *
 * Used to show the list of products (e.g. for the menu card)
 */
class ProductList
{
    /**
     * List of product numbers
     *
     * @var array
     */
    private $product_list = [];

    /**
     * List of products
     *
     * @var Product[]
     */
    private $product = [];

    /**
     * Load all products from a category
     *
     * @param string $cat
     * @return void
     */
    public function load_cat($cat)
    {
        global $db;
        $products = $db->qry("SELECT id FROM %prefix%food_product WHERE cat_id=%string%", $cat);

        $i = 0;
        while ($data = $db->fetch_array($products)) {
            $this->product_list[$i] .= $data['id'];
            $this->product[$i] = new Product($data['id']);
            $i++;
        }
    }

    /**
     * Productlist for output
     *
     * @param string $worklink
     * @return void
     */
    public function get_list($worklink)
    {
        global $dsp;

        if (count($this->product) > 0) {
            for ($i = 0; $i < count($this->product); $i++) {
                $this->product[$i]->order_form($worklink);
            }
        } else {
            $dsp->AddSingleRow(t('In dieser Kategorie sind keine Produkte vorhanden'));
        }
    }

    /**
     * Detail view of product
     *
     * @param int $id
     * @param string $worklink
     * @return void
     */
    public function get_info($id, $worklink)
    {
        $data_array = array_flip($this->product_list);
        $this->product[$data_array[$id]]->get_info($worklink);
    }

    /**
     * Add a product to the list.
     * Returns true once the product is added, false otherwise
     *
     * @param int       $id
     * @param array|int $opt
     * @return bool
     */
    public function add_product($id, $opt)
    {
        // Product already in the list?
        if (in_array($id, $this->product_list)) {
            if (is_array($opt)) {
                $temp_prod = new Product($id);
                $temp_prod->ordered++;

                foreach ($opt as $key => $value) {
                    $temp_prod->order_option($key);
                }

                // Search in the list for the same product
                foreach ($this->product_list as $key => $value) {
                    if ($value == $id) {
                        // If the product is the same, just add it once
                        if ($this->product[$key]->compare($temp_prod)) {
                            $this->product[$key]->ordered++;

                            return true;
                        }
                    }
                }

                // If it is not the same product, get the last key
                end($this->product);
                $key_array = each($this->product);
                if (count($this->product) == 0) {
                    $key = 0;
                } else {
                    $key = $key_array[0] + 1;
                }

                // and add the product
                $this->product[$key] = new Product($id);
                $this->product[$key]->ordered++;
                $this->product_list[] = $id;

                foreach ($opt as $cle => $value) {
                    $this->product[$key]->order_option($cle);
                }

                return true;
            } else {
                // If the product is not given, search for it
                foreach ($this->product_list as $key => $value) {
                    if ($value == $id) {
                        $this->product[$key]->order_option($opt, 0);
                        return true;
                    }
                }

                return false;
            }

            // Product not in there yet, add it
        } else {
            $ret = true;

            end($this->product);
            $key_array = each($this->product);
            if (count($this->product) == 0) {
                $key = 0;
            } else {
                $key = $key_array[0] + 1;
            }

            // Add the product
            $this->product[$key] = new Product($id);
            $this->product[$key]->ordered++;
            $this->product_list[] = $id;

            if (is_array($opt)) {
                foreach ($opt as $cle => $value) {
                    if (!$this->product[$key]->order_option($cle)) {
                        $ret = false;
                    }
                }
            } else {
                $ret = $this->product[$key]->order_option($opt);
            }

            return $ret;
        }
    }

    /**
     * Write new basket once something changed
     *
     * @param int       $listid
     * @param array|int $opt
     * @param int $value
     * @return mixed
     */
    public function chanche_ordered($listid, $opt, $value)
    {
        if (!is_null($opt)) {
            return $this->product[$listid]->order_option($opt, $value);
        }

        return $this->product[$listid]->set_ordered($value);
    }

    /**
     * Remove empty products from the list
     *
     * @return void
     */
    public function check_list()
    {
        foreach ($this->product_list as $key => $value) {
            if ($this->product[$key]->count_unit() == 0) {
                unset($this->product[$key]);
                unset($this->product_list[$key]);
            }
        }
    }

    /**
     * Create form for the basket
     * @return void
     */
    public function get_basket_form()
    {
        foreach ($this->product_list as $key => $value) {
            $this->product[$key]->get_basket($key);
        }
    }

    /**
     * Count products
     *
     * @return int
     */
    public function count_products()
    {
        $count = 0;
        foreach ($this->product_list as $key => $value) {
            $count += $this->product[$key]->count_unit();
        }

        return $count;
    }

    /**
     * Sum up product prices
     *
     * @return int
     */
    public function count_products_price()
    {
        $price = 0;
        foreach ($this->product_list as $key => $value) {
            $price += $this->product[$key]->count_price();
        }
        return $price;
    }

    /**
     * Order product
     *
     * @param int $userid
     * @param array $delivered
     * @return int
     */
    public function order_product($userid, $delivered)
    {
        $price = 0;
        foreach ($this->product_list as $key => $value) {
            $price += $this->product[$key]->order($userid, $delivered);
        }

        return $price;
    }

    /**
     * @param int $userid
     * @param array $delivered
     * @return string
     */
    public function order_productdesc($userid, $delivered)
    {
        $tempdesc = "";
        foreach ($this->product_list as $key => $value) {
            $tempdesc .= " ".$this->product[$key]->caption." *";
        }

        return $tempdesc;
    }
}
