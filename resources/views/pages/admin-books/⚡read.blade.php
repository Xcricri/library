<?php

use Livewire\Component;
use App\Models\Book;

new class extends Component {
    public Book $book;
    public $pdfUrl;

    public function mount($slug)
    {
        $this->book = Book::where('slug', $slug)->firstOrFail();
        $this->pdfUrl = route('admin.books.pdf', $this->book->slug);
    }

    public function render()
    {
        return $this->view()->layout('layouts::dashboard')->title('Stream Book');
    }
};
?>

<div class="p-4 rounded-lg shadow">
    <div x-data="pdfViewer('{{ $pdfUrl }}')" x-init="initPdf()">

        <!-- Navigation bar -->
        <div class="flex items-center justify-center gap-4 mb-4">
            <button @click="prevPage()" :disabled="pageNum <= 1"
                class= "px-4 bg-blue-600 py-2 bg-blue-60 rounded disabled:opacity-50">
                Sebelumnya
            </button>

            <span>
                Halaman: <span x-text="pageNum"></span> / <span x-text="pageCount"></span>
            </span>

            <button @click="nextPage()" :disabled="pageNum >= pageCount"
                class="px-4 py-2 bg-blue-600 rounded disabled:opacity-50">
                Selanjutnya
            </button>
        </div>

        <div wire:ignore class="flex justify-center  p-2 overflow-auto" style="min-height: 500px;">
            <canvas id="pdf-canvas" class="shadow-lg"></canvas>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            let pdfDocInstance = null;

            Alpine.data('pdfViewer', (pdfUrl) => ({
                url: pdfUrl,
                pageNum: 1,
                pageCount: 0,
                pageIsRendering: false,
                pageNumIsPending: null,
                scale: 1.5,
                canvas: null,
                ctx: null,

                initPdf() {

                    this.canvas = document.getElementById('pdf-canvas');
                    this.ctx = this.canvas.getContext('2d');

                    pdfjsLib.getDocument(this.url).promise.then(doc => {
                        pdfDocInstance = doc;
                        this.pageCount = doc.numPages;
                        this.renderPage(this.pageNum);
                    }).catch(err => {
                        console.error('Gagal memuat PDF:', err);
                    });
                },

                renderPage(num) {
                    this.pageIsRendering = true;
                    this.pageNum = num;

                    pdfDocInstance.getPage(num).then(page => {
                        const viewport = page.getViewport({
                            scale: this.scale
                        });
                        this.canvas.height = viewport.height;
                        this.canvas.width = viewport.width;

                        const renderCtx = {
                            canvasContext: this.ctx,
                            viewport: viewport
                        };

                        page.render(renderCtx).promise.then(() => {
                            this.pageIsRendering = false;
                            if (this.pageNumIsPending !== null) {
                                this.renderPage(this.pageNumIsPending);
                                this.pageNumIsPending = null;
                            }
                        });
                    }).catch(err => {
                        console.error('Gagal memproses halaman:', err);
                        this.pageIsRendering = false;
                    });
                },

                queueRenderPage(num) {
                    if (this.pageIsRendering) {
                        this.pageNumIsPending = num;
                    } else {
                        this.renderPage(num);
                    }
                },

                prevPage() {
                    if (this.pageNum <= 1) return;
                    this.queueRenderPage(this.pageNum - 1);
                },

                nextPage() {
                    if (this.pageNum >= this.pageCount) return;
                    this.queueRenderPage(this.pageNum + 1);
                }
            }));
        });
    </script>
</div>
