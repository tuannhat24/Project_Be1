<?php
class Pagination {
    private $totalItems;      // Tổng số items
    private $itemsPerPage;    // Số items trên mỗi trang
    private $currentPage;     // Trang hiện tại
    private $totalPages;      // Tổng số trang
    private $offset;          // Vị trí bắt đầu lấy dữ liệu
    
    public function __construct($totalItems, $itemsPerPage = 10, $currentPage = 1) {
        $this->totalItems = $totalItems;
        $this->itemsPerPage = $itemsPerPage;
        $this->currentPage = (int)$currentPage;
        $this->totalPages = ceil($this->totalItems / $this->itemsPerPage);
        
        // Đảm bảo currentPage không vượt quá totalPages
        if ($this->currentPage > $this->totalPages) {
            $this->currentPage = $this->totalPages;
        }
        if ($this->currentPage < 1) {
            $this->currentPage = 1;
        }
        
        $this->offset = ($this->currentPage - 1) * $this->itemsPerPage;
    }
    
    // Lấy vị trí bắt đầu
    public function getOffset() {
        return $this->offset;
    }
    
    // Lấy số items trên mỗi trang
    public function getLimit() {
        return $this->itemsPerPage;
    }
    
    // Tạo HTML cho phân trang
    public function createLinks($baseUrl) {
        if ($this->totalPages <= 1) return '';
        
        $html = '<nav aria-label="Page navigation" class="my-4">
                 <ul class="pagination pagination-custom justify-content-center">';
        
        // Nút Previous
        if ($this->currentPage > 1) {
            $html .= sprintf(
                '<li class="page-item"><a class="page-link" href="%s?page=%d">&lt;</a></li>',
                $baseUrl,
                $this->currentPage - 1
            );
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">&lt;</span></li>';
        }
        
        // Hiển thị các trang
        $start = max(1, $this->currentPage - 2);
        $end = min($this->totalPages, $this->currentPage + 2);
        
        // Hiển thị trang đầu nếu cần
        if ($start > 1) {
            $html .= sprintf(
                '<li class="page-item"><a class="page-link" href="%s?page=1">1</a></li>',
                $baseUrl
            );
            if ($start > 2) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }
        
        // Hiển thị các trang giữa
        for ($i = $start; $i <= $end; $i++) {
            if ($i == $this->currentPage) {
                $html .= sprintf(
                    '<li class="page-item active"><span class="page-link">%d</span></li>',
                    $i
                );
            } else {
                $html .= sprintf(
                    '<li class="page-item"><a class="page-link" href="%s?page=%d">%d</a></li>',
                    $baseUrl,
                    $i,
                    $i
                );
            }
        }
        
        // Hiển thị trang cuối nếu cần
        if ($end < $this->totalPages) {
            if ($end < $this->totalPages - 1) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            $html .= sprintf(
                '<li class="page-item"><a class="page-link" href="%s?page=%d">%d</a></li>',
                $baseUrl,
                $this->totalPages,
                $this->totalPages
            );
        }
        
        // Nút Next
        if ($this->currentPage < $this->totalPages) {
            $html .= sprintf(
                '<li class="page-item"><a class="page-link" href="%s?page=%d">&gt;</a></li>',
                $baseUrl,
                $this->currentPage + 1
            );
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">&gt;</span></li>';
        }
        
        $html .= '</ul></nav>';
        
        return $html;
    }
    
    // Giữ nguyên các query parameters khác khi phân trang
    public function createLinksWithQuery($baseUrl, $queryParams = []) {
        // Loại bỏ tham số page từ query params nếu có
        unset($queryParams['page']);
        
        // Tạo query string từ các tham số
        $queryString = http_build_query($queryParams);
        
        // Thêm ? hoặc & tùy thuộc vào baseUrl đã có query string chưa
        $baseUrl .= (strpos($baseUrl, '?') === false) ? '?' : '&';
        
        if (!empty($queryParams)) {
            $baseUrl .= $queryString . '&';
        }
        
        return $this->createLinks($baseUrl);
    }
} 