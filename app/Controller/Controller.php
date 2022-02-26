<?php

namespace Common\Library\Traits;

trait Controller
{

    /**
     * 查看
     */
    public function index()
    {
        if ($this->request->ajax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->query('keyField')) {
                return $this->selectpage();
            }
            $search = $this->request->get("search", '');
            $filter = $this->request->get("filter", '');
            $op = $this->request->get("op", '', 'trim');
            $sort = $this->request->get("sort", 'id');
            $order = $this->request->get("order", "DESC");
            $offset = $this->request->get("offset", 0);
            $limit = $this->request->get("limit", 999999);

            $list = $this->model->offset($offset)->limit($limit)->get();

            return ["total" => $list->count(), "rows" => $list->all()];
        }

        $this->build_heading();

        return $this->view->fetch();
    }

    /**
     * 回收站
     */
    public function recyclebin()
    {
        if ($this->request->ajax()) {
            $search = $this->request->get("search", '');
            $filter = $this->request->get("filter", '');
            $op = $this->request->get("op", '', 'trim');
            $sort = $this->request->get("sort", 'id');
            $order = $this->request->get("order", "DESC");
            $offset = $this->request->get("offset", 0);
            $limit = $this->request->get("limit", 999999);

            $list = $this->model->offset($offset)->limit($limit)->get();

            return ["total" => $list->count(), "rows" => $list->all()];
        }

        return $this->view->fetch();
    }

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->method() == 'POST') {
            event(CsrfTokenEvent::class);
            $params = $this->request->post("row", []);
            if ($params) {
                $result = false;
                $this->db->beginTransaction();
                try {
                    $result = $this->model->create($params);
                    $this->db->commit();
                } catch (\PDOException $e) {
                    $this->db->rollback();
                    $this->error($e->getMessage());
                } catch (\Exception $e) {
                    $this->db->rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $ids = $ids ? $ids : $this->request->query("ids");
        $row = $this->model->where($this->model->getKeyName(), $ids)->first();
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        if ($this->request->method() == 'POST') {
            $params = $this->request->post("row");
            if ($params) {
                $result = false;
                $this->db->beginTransaction();
                try {
                    $result = $row->update($params);
                    $this->db->commit();
                } catch (\PDOException $e) {
                    $this->db->rollback();
                    $this->error($e->getMessage());
                } catch (\Exception $e) {
                    $this->db->rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were updated'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->share("row", $row);

        return $this->view->fetch();
    }

    /**
     * 删除
     */
    public function del($ids = null)
    {
        if (!$this->request->method() == 'POST') {
            $this->error(__("Invalid parameters"));
        }
        $ids = $ids ? $ids : $this->request->post("ids");
        if ($ids) {
            $this->db->beginTransaction();
            try {
                $this->model->whereIn($this->model->getKeyName(), $ids)->delete();
                $this->db->commit();
            } catch (\PDOException $e) {
                $this->db->rollback();
                $this->error($e->getMessage());
            } catch (\Exception $e) {
                $this->db->rollback();
                $this->error($e->getMessage());
            }
            $this->success();
        }
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }

    /**
     * 批量更新
     */
    public function multi($ids = null)
    {
        if (!$this->request->method() == 'POST') {
            $this->error(__("Invalid parameters"));
        }
        $ids = $ids ? $ids : $this->request->post("ids");
        $ids = explode(',', $ids);
        if ($ids) {
            if ($this->request->has('params')) {
                parse_str($this->request->post("params"), $values);
                if ($values) {
                    $this->db->beginTransaction();
                    try {
                        $this->model->whereIn($this->model->getKeyName(), $ids)->update($values);
                        $this->db->commit();
                    } catch (\PDOException $e) {
                        $this->db->rollback();
                        $this->error($e->getMessage());
                    } catch (\Exception $e) {
                        $this->db->rollback();
                        $this->error($e->getMessage());
                    }
                    $this->success();
                } else {
                    $this->error(__('You have no permission'));
                }
            }
        }
        $this->error(__('Parameter :param can not be empty', ['param' => 'ids']));
    }

    /**
     * 导入
     */
    protected function import()
    {
        $file = $this->request->request('file');
        if (!$file) {
            $this->error(__('Parameter %s can not be empty', 'file'));
        }
        $filePath = ROOT_PATH . DS . 'public' . DS . $file;
        if (!is_file($filePath)) {
            $this->error(__('No results were found'));
        }
        //实例化reader
        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
        if (!in_array($ext, ['csv', 'xls', 'xlsx'])) {
            $this->error(__('Unknown data format'));
        }
        if ($ext === 'csv') {
            $file = fopen($filePath, 'r');
            $filePath = tempnam(sys_get_temp_dir(), 'import_csv');
            $fp = fopen($filePath, "w");
            $n = 0;
            while ($line = fgets($file)) {
                $line = rtrim($line, "\n\r\0");
                $encoding = mb_detect_encoding($line, ['utf-8', 'gbk', 'latin1', 'big5']);
                if ($encoding != 'utf-8') {
                    $line = mb_convert_encoding($line, 'utf-8', $encoding);
                }
                if ($n == 0 || preg_match('/^".*"$/', $line)) {
                    fwrite($fp, $line . "\n");
                } else {
                    fwrite($fp, '"' . str_replace(['"', ','], ['""', '","'], $line) . "\"\n");
                }
                $n++;
            }
            fclose($file) || fclose($fp);

            $reader = new Csv();
        } elseif ($ext === 'xls') {
            $reader = new Xls();
        } else {
            $reader = new Xlsx();
        }

        //导入文件首行类型,默认是注释,如果需要使用字段名称请使用name
        $importHeadType = isset($this->importHeadType) ? $this->importHeadType : 'comment';

        $table = $this->model->getQuery()->getTable();
        $database = \think\Config::get('database.database');
        $fieldArr = [];
        $list = db()->query("SELECT COLUMN_NAME,COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ? AND TABLE_SCHEMA = ?", [$table, $database]);
        foreach ($list as $k => $v) {
            if ($importHeadType == 'comment') {
                $fieldArr[$v['COLUMN_COMMENT']] = $v['COLUMN_NAME'];
            } else {
                $fieldArr[$v['COLUMN_NAME']] = $v['COLUMN_NAME'];
            }
        }

        //加载文件
        $insert = [];
        try {
            if (!$PHPExcel = $reader->load($filePath)) {
                $this->error(__('Unknown data format'));
            }
            $currentSheet = $PHPExcel->getSheet(0);  //读取文件中的第一个工作表
            $allColumn = $currentSheet->getHighestDataColumn(); //取得最大的列号
            $allRow = $currentSheet->getHighestRow(); //取得一共有多少行
            $maxColumnNumber = Coordinate::columnIndexFromString($allColumn);
            $fields = [];
            for ($currentRow = 1; $currentRow <= 1; $currentRow++) {
                for ($currentColumn = 1; $currentColumn <= $maxColumnNumber; $currentColumn++) {
                    $val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
                    $fields[] = $val;
                }
            }

            for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
                $values = [];
                for ($currentColumn = 1; $currentColumn <= $maxColumnNumber; $currentColumn++) {
                    $val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
                    $values[] = is_null($val) ? '' : $val;
                }
                $row = [];
                $temp = array_combine($fields, $values);
                foreach ($temp as $k => $v) {
                    if (isset($fieldArr[$k]) && $k !== '') {
                        $row[$fieldArr[$k]] = $v;
                    }
                }
                if ($row) {
                    $insert[] = $row;
                }
            }
        } catch (Exception $exception) {
            $this->error($exception->getMessage());
        }
        if (!$insert) {
            $this->error(__('No rows were updated'));
        }

        try {
            //是否包含admin_id字段
            $has_admin_id = false;
            foreach ($fieldArr as $name => $key) {
                if ($key == 'admin_id') {
                    $has_admin_id = true;
                    break;
                }
            }
            if ($has_admin_id) {
                $auth = Auth::instance();
                foreach ($insert as &$val) {
                    if (!isset($val['admin_id']) || empty($val['admin_id'])) {
                        $val['admin_id'] = $auth->isLogin() ? $auth->id : 0;
                    }
                }
            }
            $this->model->saveAll($insert);
        } catch (PDOException $exception) {
            $msg = $exception->getMessage();
            if (preg_match("/.+Integrity constraint violation: 1062 Duplicate entry '(.+)' for key '(.+)'/is", $msg, $matches)) {
                $msg = "导入失败，包含【{$matches[1]}】的记录已存在";
            };
            $this->error($msg);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }

        $this->success();
    }
}
