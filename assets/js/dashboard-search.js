/**
 * Dashboard Search - Sistema de busca otimizado
 * Busca client-side em tempo real com fallback AJAX
 */
class DashboardSearch {
    constructor() {
        this.init();
    }
    
    init() {
        this.$searchInput = $('#search-input');
        this.$statusFilter = $('#status-filter');
        this.$patientsGrid = $('#patients-grid');
        this.$patientCards = this.$patientsGrid.find('.patient-card');
        this.$resultsCounter = $('#results-counter');
        
        this.searchTimeout = null;
        this.setupEventListeners();
        this.updateResultsCounter(this.$patientCards.length, this.$patientCards.length);
    }
    
    setupEventListeners() {
        this.$searchInput.on('input', () => {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => this.performClientSearch(), 150);
        });
        
        this.$statusFilter.on('change', () => this.performClientSearch());
        
        // Clear search
        this.$searchInput.on('keydown', (e) => {
            if (e.key === 'Escape') {
                this.$searchInput.val('');
                this.performClientSearch();
            }
        });
    }
    
    performClientSearch() {
        const searchTerm = this.$searchInput.val().toLowerCase().trim();
        const statusFilter = this.$statusFilter.val();
        let visibleCount = 0;
        
        this.$patientCards.each((index, element) => {
            const $card = $(element);
            const searchableText = $card.find('[data-searchable]').text().toLowerCase();
            const cardStatus = $card.data('status');
            
            const matchesSearch = !searchTerm || searchableText.includes(searchTerm);
            const matchesStatus = !statusFilter || cardStatus === statusFilter;
            const shouldShow = matchesSearch && matchesStatus;
            
            $card.toggle(shouldShow);
            if (shouldShow) visibleCount++;
        });
        
        this.updateResultsCounter(visibleCount, this.$patientCards.length);
        this.highlightSearchTerms(searchTerm);
    }
    
    highlightSearchTerms(searchTerm) {
        if (!searchTerm) {
            this.$patientCards.find('[data-searchable]').removeClass('search-highlight');
            return;
        }
        
        this.$patientCards.find('[data-searchable]:visible').each((index, element) => {
            const $element = $(element);
            const text = $element.text();
            
            if (text.toLowerCase().includes(searchTerm)) {
                $element.addClass('search-highlight');
            } else {
                $element.removeClass('search-highlight');
            }
        });
    }
    
    updateResultsCounter(visible, total) {
        const counterHtml = `
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                ${visible} de ${total} associados
            </span>
        `;
        this.$resultsCounter.html(counterHtml);
    }
}

// Inicializar quando o DOM estiver pronto
jQuery(document).ready(() => {
    new DashboardSearch();
});