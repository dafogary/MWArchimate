document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.mwarchimate-container').forEach(container => {
    const cyDiv = document.createElement('div');
    cyDiv.style.width = '100%';
    cyDiv.style.height = '600px';
    container.appendChild(cyDiv);

    const rawXML = container.dataset.xml;
    console.log("XML:", rawXML);

    // Parse XML
    const parser = new DOMParser();
    const xmlDoc = parser.parseFromString(rawXML, 'text/xml');

    // Example: Find elements and relationships (adjust tags as needed for your XML)
    const elements = [];
    const relationships = [];

    xmlDoc.querySelectorAll('element').forEach(el => {
      const id = el.getAttribute('identifier') || el.getAttribute('id');
      const label = el.getAttribute('name') || 'Unnamed';
      elements.push({ data: { id, label } });
    });

    xmlDoc.querySelectorAll('relationship').forEach(rel => {
      const id = rel.getAttribute('identifier') || rel.getAttribute('id');
      const source = rel.getAttribute('source');
      const target = rel.getAttribute('target');
      relationships.push({ data: { id, source, target } });
    });

    // Render with Cytoscape
    cytoscape({
      container: cyDiv,
      elements: [...elements, ...relationships],
      style: [
        {
          selector: 'node',
          style: {
            'label': 'data(label)'
          }
        },
        {
          selector: 'edge',
          style: {
            'label': 'data(label)'
          }
        }
      ],
      layout: {
        name: 'grid'
      }
    });
  });
});