/**
 * ArchiMate Viewer and Analyzer
 * Renders ArchiMate models using Cytoscape.js
 */

// Declare variables to be exported globally
var loadModel, showError;

(function() {
    // Initialize when document is ready
    $(document).ready(function() {
        initViewer();
    });

    function initViewer() {
        // Create container element
        const container = $('<div class="archimate-viewer"></div>');
        const cyContainer = $('<div id="cy"></div>');
        const loadingIndicator = $('<div class="loading"></div>');
        const controls = createControls();
        
        container.append(cyContainer);
        container.append(controls);
        container.append(loadingIndicator);
        $('body').append(container);
        
        // Get model URL from query parameters
        const urlParams = new URLSearchParams(window.location.search);
        const modelUrl = urlParams.get('model');
        
        if (!modelUrl) {
            showError("No ArchiMate model URL provided");
            return;
        }
        
        // Load the model
        loadModel(modelUrl);
    }
    
    function createControls() {
        const controls = $('<div class="viewer-controls"></div>');
        controls.append('<button id="zoom-in">+</button>');
        controls.append('<button id="zoom-out">-</button>');
        controls.append('<button id="fit">Fit</button>');
        
        // Attach event handlers after the viewer is initialized
        setTimeout(function() {
            $('#zoom-in').on('click', function() {
                cy.zoom(cy.zoom() * 1.2);
            });
            
            $('#zoom-out').on('click', function() {
                cy.zoom(cy.zoom() * 0.8);
            });
            
            $('#fit').on('click', function() {
                cy.fit();
            });
        }, 1000);
        
        return controls;
    }
    
    // Export loadModel to global scope
    loadModel = function(url) {
        $.ajax({
            url: url,
            dataType: 'xml',
            success: function(data) {
                $('.loading').hide();
                parseAndRenderArchiMateModel(data);
            },
            error: function(xhr, status, error) {
                showError("Failed to load model: " + error);
            }
        });
    };
    
    function parseAndRenderArchiMateModel(xmlData) {
        // Convert XML to Cytoscape elements
        const elements = convertArchiMateToElements(xmlData);
        
        // Initialize Cytoscape
        window.cy = cytoscape({
            container: document.getElementById('cy'),
            elements: elements,
            style: [
                {
                    selector: 'node',
                    style: {
                        'label': 'data(name)',
                        'text-valign': 'center',
                        'text-halign': 'center',
                        'background-color': 'data(color)',
                        'shape': 'roundrectangle',
                        'width': 'label',
                        'height': 'label',
                        'padding': '10px'
                    }
                },
                {
                    selector: 'edge',
                    style: {
                        'width': 2,
                        'line-color': '#999',
                        'target-arrow-color': '#999',
                        'target-arrow-shape': 'triangle',
                        'curve-style': 'bezier'
                    }
                }
            ],
            layout: {
                name: 'cose',
                animate: false,
                nodeDimensionsIncludeLabels: true
            }
        });
    }
    
    function convertArchiMateToElements(xmlData) {
        // This is a simplified parser for ArchiMate XML
        // You'll need to expand this based on the structure of your ArchiMate files
        const elements = [];
        
        try {
            const $xml = $(xmlData);
            
            // Extract nodes
            $xml.find('element').each(function() {
                const $element = $(this);
                const id = $element.attr('id') || $element.attr('identifier');
                const name = $element.attr('name') || $element.find('name').text() || id;
                const type = $element.attr('xsi:type') || $element.attr('type');
                
                // Determine color based on type
                let color = '#cccccc';  // Default gray
                if (type) {
                    if (type.includes('Business')) color = '#ffffb3';
                    else if (type.includes('Application')) color = '#b3e6ff';
                    else if (type.includes('Technology')) color = '#c6ebc6';
                    else if (type.includes('Motivation')) color = '#ffe6cc';
                    else if (type.includes('Implementation')) color = '#e6ccff';
                }
                
                elements.push({
                    data: {
                        id: id,
                        name: name,
                        type: type,
                        color: color
                    }
                });
            });
            
            // Extract relationships
            $xml.find('relationship').each(function() {
                const $rel = $(this);
                const id = $rel.attr('id') || $rel.attr('identifier');
                const source = $rel.attr('source') || $rel.find('source').text();
                const target = $rel.attr('target') || $rel.find('target').text();
                
                if (source && target) {
                    elements.push({
                        data: {
                            id: id,
                            source: source,
                            target: target
                        }
                    });
                }
            });
            
            return elements;
        } catch (e) {
            showError("Error parsing ArchiMate model: " + e.message);
            return [];
        }
    }
    
    // Export showError to global scope
    showError = function(message) {
        $('.loading').hide();
        $('<div class="error-display">' + message + '</div>').appendTo('.archimate-viewer');
    };
})();