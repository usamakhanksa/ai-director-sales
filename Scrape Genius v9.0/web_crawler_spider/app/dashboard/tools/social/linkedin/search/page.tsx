'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import DashboardShell from '../../../../../../components/DashboardShell';
import { getToken } from '../../../../../../lib/client-auth';

interface LinkedInProfileResult {
  fullName: string;
  title: string;
  location: string;
  profileUrl: string;
  email?: string;
  phone?: string;
  description?: string;
}

const LinkedInSearchPage = () => {
  const [keyword, setKeyword] = useState('');
  const [sessionCookie, setSessionCookie] = useState('');
  const [limit, setLimit] = useState(10);
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [results, setResults] = useState<LinkedInProfileResult[]>([]);
  const [showContactInfo, setShowContactInfo] = useState(true);

  const handleSearch = async (e: React.FormEvent) => {
    e.preventDefault();
    setError(null);
    setResults([]);
    setIsLoading(true);

    try {
      // Validate inputs
      if (!keyword || !sessionCookie) {
        throw new Error('Both keyword and session cookie are required');
      }

      const token = getToken();
      if (!token) {
        throw new Error('You must be signed in to search LinkedIn profiles');
      }

      // Make API call to search LinkedIn profiles
      const response = await fetch('/api/social/linkedin/search', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify({
          keyword,
          sessionCookieValue: sessionCookie,
          limit
        }),
      });

      const json = await response.json();

      if (!response.ok || !json.success) {
        throw new Error(json.error || 'Failed to search LinkedIn profiles');
      }

      // Process the results
      setResults(json.data?.results || []);
    } catch (err: any) {
      console.error('LinkedIn search error:', err);
      setError(err.message || 'An error occurred while searching LinkedIn profiles');
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <DashboardShell>
      <div className="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div className="mb-8">
          <h1 className="text-3xl font-bold text-gray-900">LinkedIn Search</h1>
          <p className="mt-2 text-lg text-gray-600">
            Search for LinkedIn profiles by keyword and extract contact information when available
          </p>
        </div>
        
        {/* Navigation Tabs */}
        <div className="border-b border-gray-200 mb-6">
          <nav className="-mb-px flex space-x-8">
            <Link
              href="/dashboard/tools/social/linkedin"
              className="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
            >
              Profile Scraper
            </Link>
            <Link
              href="/dashboard/tools/social/linkedin/search"
              className="border-blue-500 text-blue-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
            >
              Search Profiles
            </Link>
          </nav>
        </div>
        
        <div className="bg-white shadow rounded-lg p-6 mb-8">
          <form onSubmit={handleSearch} className="space-y-6">
            <div>
              <label htmlFor="keyword" className="block text-sm font-medium text-gray-700 mb-1">
                Search Keyword
              </label>
              <input
                type="text"
                id="keyword"
                value={keyword}
                onChange={(e) => setKeyword(e.target.value)}
                placeholder="e.g., hotel owner, software engineer"
                className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                required
              />
              <p className="mt-1 text-xs text-gray-500">
                Enter a keyword to search for profiles (e.g., job title, industry)
              </p>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label htmlFor="sessionCookie" className="block text-sm font-medium text-gray-700 mb-1">
                  Session Cookie Value
                </label>
                <input
                  type="password"
                  id="sessionCookie"
                  value={sessionCookie}
                  onChange={(e) => setSessionCookie(e.target.value)}
                  placeholder="Enter your li_at cookie value"
                  className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  required
                />
              </div>

              <div>
                <label htmlFor="limit" className="block text-sm font-medium text-gray-700 mb-1">
                  Limit Results
                </label>
                <select
                  id="limit"
                  value={limit}
                  onChange={(e) => setLimit(parseInt(e.target.value))}
                  className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value={5}>5 profiles</option>
                  <option value={10}>10 profiles</option>
                  <option value={20}>20 profiles</option>
                  <option value={50}>50 profiles</option>
                </select>
              </div>
            </div>

            <div className="flex items-center">
              <input
                type="checkbox"
                id="showContactInfo"
                checked={showContactInfo}
                onChange={(e) => setShowContactInfo(e.target.checked)}
                className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              />
              <label htmlFor="showContactInfo" className="ml-2 block text-sm text-gray-700">
                Show contact information when available
              </label>
            </div>

            <button
              type="submit"
              disabled={isLoading}
              className={`w-full py-3 px-4 rounded-md text-white font-medium ${
                isLoading 
                  ? 'bg-gray-400 cursor-not-allowed' 
                  : 'bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500'
              }`}
            >
              {isLoading ? (
                <span className="flex items-center justify-center">
                  <svg className="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Searching...
                </span>
              ) : (
                'Search LinkedIn Profiles'
              )}
            </button>
          </form>
        </div>

        {error && (
          <div className="mt-6 p-4 bg-red-50 border border-red-200 rounded-md">
            <h3 className="text-red-800 font-medium">Error</h3>
            <p className="text-red-600">{error}</p>
          </div>
        )}

        {results.length > 0 && (
          <div className="bg-white shadow rounded-lg p-6">
            <div className="flex justify-between items-center mb-4">
              <h3 className="text-xl font-semibold text-gray-800">
                Search Results ({results.length})
              </h3>
              <div className="text-sm text-gray-500">
                Showing {Math.min(results.length, limit)} of {results.length} results
              </div>
            </div>

            <div className="overflow-x-auto">
              <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-gray-50">
                  <tr>
                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Name
                    </th>
                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Title
                    </th>
                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Location
                    </th>
                    {showContactInfo && (
                      <>
                        <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Email
                        </th>
                        <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Phone
                        </th>
                      </>
                    )}
                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody className="bg-white divide-y divide-gray-200">
                  {results.map((result, index) => (
                    <tr key={index} className={index % 2 === 0 ? 'bg-white' : 'bg-gray-50'}>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <div className="text-sm font-medium text-gray-900">{result.fullName}</div>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <div className="text-sm text-gray-900">{result.title}</div>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {result.location}
                      </td>
                      {showContactInfo && (
                        <>
                          <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {result.email ? (
                              <a href={`mailto:${result.email}`} className="text-blue-600 hover:text-blue-900">
                                {result.email}
                              </a>
                            ) : (
                              <span className="text-gray-400">-</span>
                            )}
                          </td>
                          <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {result.phone ? (
                              <a href={`tel:${result.phone}`} className="text-blue-600 hover:text-blue-900">
                                {result.phone}
                              </a>
                            ) : (
                              <span className="text-gray-400">-</span>
                            )}
                          </td>
                        </>
                      )}
                      <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a
                          href={result.profileUrl}
                          target="_blank"
                          rel="noopener noreferrer"
                          className="text-blue-600 hover:text-blue-900"
                        >
                          View Profile
                        </a>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>

            <div className="mt-6 bg-blue-50 border-l-4 border-blue-400 p-4">
              <div className="flex">
                <div className="flex-shrink-0">
                  <svg className="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clipRule="evenodd" />
                  </svg>
                </div>
                <div className="ml-3">
                  <p className="text-sm text-blue-700">
                    <strong>Important:</strong> Contact information (email and phone) is only displayed if the profile owner has made it publicly available.
                    Most LinkedIn users do not have public contact information, so these fields will often be empty.
                  </p>
                </div>
              </div>
            </div>
          </div>
        )}

        <div className="mt-8 bg-blue-50 border-l-4 border-blue-400 p-4">
          <div className="flex">
            <div className="flex-shrink-0">
              <svg className="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clipRule="evenodd" />
              </svg>
            </div>
            <div className="ml-3">
              <p className="text-sm text-blue-700">
                <strong>How to get your LinkedIn session cookie:</strong>
              </p>
              <ol className="mt-2 list-decimal list-inside text-sm text-blue-700 space-y-1">
                <li>Login to your LinkedIn account in Chrome/Firefox</li>
                <li>Press F12 to open Developer Tools</li>
                <li>Go to the Application tab (Chrome) or Storage tab (Firefox)</li>
                <li>Under Cookies, select www.linkedin.com</li>
                <li>Find the cookie named "li_at"</li>
                <li>Copy the value and paste it in the form above</li>
              </ol>
            </div>
          </div>
        </div>
      </div>
    </DashboardShell>
  );
};

export default LinkedInSearchPage;