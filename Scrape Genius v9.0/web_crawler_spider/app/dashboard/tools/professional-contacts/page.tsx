'use client';

import { useState } from 'react';
import DashboardShell from '@/components/DashboardShell';

export default function ProfessionalContactsPage() {
  const [domain, setDomain] = useState('');
  const [keyword, setKeyword] = useState('');
  const [company, setCompany] = useState('');
  const [results, setResults] = useState<any[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  const handleSearch = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError('');
    setResults([]);

    try {
      const res = await fetch('/api/professional-contacts', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
          domain: domain || undefined, 
          keyword: keyword || undefined,
          company: company || undefined
        }),
      });

      const data = await res.json();
      if (data.success) {
        setResults(data.results);
      } else {
        setError(data.error || 'An error occurred');
      }
    } catch (err) {
      setError('Failed to fetch data: ' + (err as Error).message);
    } finally {
      setLoading(false);
    }
  };

  return (
    <DashboardShell>
      <div className="max-w-4xl mx-auto p-6">
        <div className="mb-8">
          <h1 className="text-3xl font-bold text-gray-900 mb-2">Professional Contact Finder</h1>
          <p className="text-gray-600">
            Find professional contacts using legitimate APIs instead of scraping
          </p>
        </div>

        <div className="bg-white rounded-lg shadow-md p-6 mb-8">
          <div className="prose prose-blue max-w-none mb-6">
            <h3 className="text-lg font-semibold text-gray-900 mb-3">Important Notice</h3>
            <ul className="space-y-2 text-sm text-gray-700">
              <li>• <strong>Automated scraping of LinkedIn violates their Terms of Service</strong> and may result in IP bans or legal action</li>
              <li>• Search engines actively block queries for LinkedIn profile URLs</li>
              <li>• Our recommended approach uses legitimate APIs like Hunter.io, Proxycurl, and Apollo.io</li>
              <li>• Always ensure compliance with privacy regulations like GDPR and CCPA</li>
              <li>• Respect data protection laws when processing professional contact information</li>
            </ul>
          </div>

          <form onSubmit={handleSearch} className="space-y-4">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                  Company Domain <span className="text-red-500">*</span>
                </label>
                <input
                  type="text"
                  placeholder="e.g., company.com"
                  value={domain}
                  onChange={(e) => setDomain(e.target.value)}
                  className="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border"
                  required
                />
                <p className="mt-1 text-xs text-gray-500">Enter company domain to find contacts</p>
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                  Keyword / Title (Optional)
                </label>
                <input
                  type="text"
                  placeholder="e.g., marketing manager, CEO"
                  value={keyword}
                  onChange={(e) => setKeyword(e.target.value)}
                  className="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border"
                />
                <p className="mt-1 text-xs text-gray-500">Filter by job title or department</p>
              </div>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Company Name (Alternative)
              </label>
              <input
                type="text"
                placeholder="e.g., Acme Corporation"
                value={company}
                onChange={(e) => setCompany(e.target.value)}
                className="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border"
              />
              <p className="mt-1 text-xs text-gray-500">Alternative to domain search</p>
            </div>

            <div className="flex items-center space-x-4 pt-4">
              <button
                type="submit"
                disabled={loading}
                className="bg-blue-600 text-white py-2 px-6 rounded-md hover:bg-blue-700 transition-colors disabled:bg-gray-400"
              >
                {loading ? 'Searching...' : 'Find Contacts'}
              </button>
              
              {error && (
                <div className="text-red-600 text-sm ml-4">{error}</div>
              )}
            </div>
          </form>
        </div>

        {results.length > 0 && (
          <div className="bg-white rounded-lg shadow overflow-hidden">
            <div className="p-4 bg-gray-50 border-b">
              <h2 className="text-xl font-semibold text-gray-800">Results ({results.length})</h2>
              <p className="text-sm text-gray-600 mt-1">Found using legitimate APIs</p>
            </div>
            
            <div className="divide-y divide-gray-200">
              {results.map((item, idx) => (
                <div key={idx} className="p-4 hover:bg-gray-50">
                  <div className="flex flex-col sm:flex-row sm:justify-between sm:items-start">
                    <div className="flex-1">
                      <p className="font-medium text-gray-900">{item.name}</p>
                      <p className="text-sm text-gray-500">{item.position}</p>
                      <div className="mt-1 flex items-center">
                        <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                          {item.source}
                        </span>
                      </div>
                    </div>
                    
                    <div className="mt-2 sm:mt-0 text-right">
                      <p className="text-blue-600 font-mono text-sm truncate max-w-xs">{item.email}</p>
                      {item.confidence && (
                        <p className="text-xs text-gray-400 mt-1">Confidence: {item.confidence}%</p>
                      )}
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        )}

        {results.length === 0 && !loading && (
          <div className="bg-white rounded-lg shadow p-8 text-center">
            <div className="text-gray-400 mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" className="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <h3 className="text-lg font-medium text-gray-900 mb-1">No Results Yet</h3>
            <p className="text-gray-500 max-w-md mx-auto">
              Enter a company domain to find professional contacts using legitimate APIs.
              Remember to comply with privacy regulations when using this data.
            </p>
          </div>
        )}

        <div className="mt-12 bg-blue-50 rounded-lg p-6 border border-blue-100">
          <h3 className="text-lg font-semibold text-blue-900 mb-3">Recommended API Services</h3>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div className="bg-white p-4 rounded border">
              <h4 className="font-medium text-gray-900 mb-2">Hunter.io</h4>
              <p className="text-sm text-gray-600">Find email patterns associated with company domains</p>
            </div>
            <div className="bg-white p-4 rounded border">
              <h4 className="font-medium text-gray-900 mb-2">Proxycurl</h4>
              <p className="text-sm text-gray-600">Structured LinkedIn profile data via API</p>
            </div>
            <div className="bg-white p-4 rounded border">
              <h4 className="font-medium text-gray-900 mb-2">Apollo.io</h4>
              <p className="text-sm text-gray-600">B2B filtering by job title, industry, and company size</p>
            </div>
          </div>
          <p className="text-xs text-gray-500 mt-4">
            Note: You need to register for API keys with these services to use them in production.
          </p>
        </div>
      </div>
    </DashboardShell>
  );
}