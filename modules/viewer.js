document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.mwarchimate-container').forEach(container => {
    const rawXML = container.dataset.xml;
    const parser = new DOMParser();
    const xmlDoc = parser.parseFromString(rawXML, 'text/xml');

    const elements = [];
    const relationships = [];

    const elementNodes = xmlDoc.getElementsByTagName('element');
    const relationNodes = xmlDoc.getElementsByTagName('relationship');

    Array.from(elementNodes).forEach(e => {
      const id = e.getAttribute('identifier') || e.getAttribute('id');
      const label = e.getAttribute('name') || 'Unnamed';
      const type = e.getAttribute('xsi:type') || 'Element';
      elements.push({ data: { id, label, type } });
    });

    Array.from(relationNodes).forEach(r => {
      const id = r.getAttribute('identifier') || r.getAttribute('id');
      const source = r.getAttribute('source');
      const target = r.getAttribute('target');
      const type = r.getAttribute('xsi:type') || 'relation';

      if (source && target) {
        relationships.push({
          data: {
            id: id || `${source}-${target}`,
            source,
            target,
            label: type
          }
        });
      }
    });

    const cyDiv = document.createElement('div');
    cyDiv.style.width = '100%';
    cyDiv.style.height = '600px';
    container.appendChild(cyDiv);

    cytoscape({
      container: cyDiv,
      elements: [...elements, ...relationships],
      style: [
        {
          selector: 'node',
          style: {
            'label': 'data(label)',
            'background-color': '#007acc',
            'shape': 'roundrectangle',
            'color': '#fff',
            'text-valign': 'center',
            'text-halign': 'center',
            'font-size': '12px'
          }
        },
        {
          selector: 'edge',
          style: {
            'width': 2,
            'line-color': '#999',
            'target-arrow-color': '#999',
            'target-arrow-shape': 'triangle',
            'curve-style': 'bezier',
            'label': 'data(label)',
            'font-size': '10px',
            'text-rotation': 'autorotate',
            'text-margin-y': '-10px'
          }
        }
      ],
      layout: {
        name: 'cose',
        animate: true
      }
    });
  });
});